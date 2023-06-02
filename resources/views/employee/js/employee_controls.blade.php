<script>
    
    function CreateContent(){

        var go_Shell = new sap.m.Shell({});
		
        //left page
        go_App_Left = new sap.m.App({});
        go_App_Left.addPage(create_page_menu());

        //right page
        go_App_Right = new sap.m.App({});
        go_App_Right.addPage(createEmpInfoPage());	
		go_App_Right.addPage(createEmpInfoDisplayPage());
		go_App_Right.addPage(createEmpInfoList());


        go_SplitContainer = new sap.ui.unified.SplitContainer({ content: [go_App_Right], secondaryContent: [go_App_Left]});		
        go_SplitContainer.setSecondaryContentWidth("250px");
        go_SplitContainer.setShowSecondaryContent(true);
        

        let go_App = new sap.m.App({
            pages : [go_SplitContainer]
        });

        go_Shell.setApp(go_App);
        go_Shell.setAppWidthLimited(false);
        go_Shell.placeAt("content");     
    }

    function create_page_menu(){
        let page = new sap.m.Page({}).addStyleClass('sapUiSizeCompact');
        let pageHeader  = new sap.m.Bar({enableFlexBox: false,contentMiddle:[ new sap.m.Label({text:"Action"})]});
        const menuList = new sap.m.List("MENU_LIST",{});
		const menuListTemplate = new sap.m.StandardListItem("LEFT_MENU_TEMPLATE",{
			title:"{title}",
			icon:"{icon}",
			visible:"{visible}",
			type: sap.m.ListType.Active,
			press:function(oEvent){
				
                let menu = oEvent.getSource().getBindingContext().getProperty('funct');
				let list_items = oEvent.getSource().getParent().getItems();

                for (var i = 0; i < list_items.length; i++) {
                    list_items[i].removeStyleClass('class_selected_list_item');
                   //$('LEFT_MENU_TEMPLATE-MENU_LIST-0').removeClass('class_selected_list_item');
                }

                oEvent.getSource().addStyleClass('class_selected_list_item');
				
				switch(menu){
					case "CREATE_EMPINFO" :
						screenMode._create();
					break;
					case "DISPLAY_EMPINFO" :
						go_App_Right.to('EMPINFO_PAGE_DISPLAY');
					break;
					case "LISTING_EMPINFO" :
						let response = async () => {
							let data = await EmpInfoDataOrganizer._getEmpData();
							EmpInfolisting._getData(data);
						go_App_Right.to('PAGE_EMPINFO_LISTING');
						}
						response();
					break;

				}
                
			}
		});
		
        const gt_list = [
                {
                    title   : "Add Employee Information",
					funct  	: "CREATE_EMPINFO",
                    icon    : "sap-icon://add-employee",
                    visible : true
                },
                {
                    title   : "Display Employee Information",
                    icon    : "sap-icon://activity-individual",
					funct  	: "DISPLAY_EMPINFO",
                    visible : true
                },
                {
                    title   : "Employee Information Listing",
                    icon    : "sap-icon://account",
					funct  	: "LISTING_EMPINFO",
                    visible : true
                },

        ];

        let model = new sap.ui.model.json.JSONModel();
			model.setSizeLimit(gt_list.length);
			model.setData(gt_list);

			ui('MENU_LIST').setModel(model).bindAggregation("items",{
				path:"/",
				template:ui('LEFT_MENU_TEMPLATE')
			});
		
        page.setCustomHeader(pageHeader);
		page.addContent(menuList);		
		return page;
    }

    function createEmpInfoPage(){
        let page  = new sap.m.Page("CREATE_EMPINFO_PAGE",{}).addStyleClass('sapUiSizeCompact');
        let pageHeader = new sap.m.Bar("",{  
			enableFlexBox: false,
			contentLeft:[
				new sap.m.Button({ icon:"sap-icon://nav-back",
					press:function(oEvt){
						go_App_Right.back();
					} 
				}),
				new sap.m.Button({icon:"sap-icon://menu2",
					press:function(){
						go_SplitContainer.setSecondaryContentWidth("250px");
						if(!go_SplitContainer.getShowSecondaryContent()){
							go_SplitContainer.setShowSecondaryContent(true);
						} else {							
							go_SplitContainer.setShowSecondaryContent(false);
						}
					
					}
				}), 
				
			],
			contentMiddle:[
                new sap.m.Label("EMPINFO_TITLE",{text:"Create Employee's Information"})
            ],
		
		});
        let crumbs = new sap.m.Breadcrumbs("CREATE_EMPINFO_BRDCRMS",{
            currentLocationText: "Create Employee's Information",
            links: [
				new sap.m.Link({
        text: "Home",
        press: function(oEvt) {
            // fn_click_breadcrumbs("HOME");
        }
    }).attachPress(function() {
        window.location.href = ""; // Home button is where you will input a new employee info
    }),
]
        });
		let errorPanel = new sap.m.Panel("MESSAGE_STRIP_EMPINFO_ERROR",{visible:false});
        let createPageFormHeader = new sap.uxap.ObjectPageLayout({
            headerTitle:[
                new sap.uxap.ObjectPageHeader("OBJECTHEADER_EMPINFO_NAME",{
                    objectTitle:"",
					showPlaceholder : false,
					actions:[
                        new sap.uxap.ObjectPageHeaderActionButton("CREATE_EMPINFO_SAVE_BTN1",{
                            icon: "sap-icon://save",
							press: function(evt){
								createEmpInfo();

                            }
                        }).addStyleClass("sapMTB-Transparent-CTX"),
                        new sap.uxap.ObjectPageHeaderActionButton("CREATE_EMPINFO_EDIT_BTN1",{
                            icon: "sap-icon://edit",
							press: function(){
									ui("COMPCODE_SAVE_DIALOG").open();
                            }
                        }).addStyleClass("sapMTB-Transparent-CTX"),

                    ],
                })
            ]     
        });

		let createPageFormContent = new sap.m.Panel("EMPINFO_GENERAL_PANEL",{
			headerToolbar: [
				new sap.m.Toolbar({
                    content: [
                        new sap.m.ToolbarSpacer(),
                        new sap.m.Button("CREATE_EMPINFO_SAVE_BTN", {
                            visible: true,
                            icon: "sap-icon://save",
                            press: function () {
								ui('EMP_ID').setValueState("None").setValueStateText("");
								ui('MESSAGE_STRIP_EMPINFO_ERROR').destroyContent().setVisible(false);
								let empID = ui('EMP_ID').getValue().trim();
								let message = "";
								let lv_message_strip = "";
									if(empID){
										if(screenMode._mode == "create"){
											let isExist = EmpInfoDataOrganizer._validateEmpID(empID);
											if(isExist){
												message = "Employee ID already exist.";
												ui('EMP_ID').setValueState("Error").setValueStateText(message);
												lv_message_strip = fn_show_message_strip("MESSAGE_STRIP_EMPINFO_ERROR",message);
												ui('MESSAGE_STRIP_EMPINFO_ERROR').setVisible(true).addContent(lv_message_strip);
											}else{
												ui('EMPINFO_SAVE_DIALOG').open();
											}
										}else{
											ui('EMPINFO_SAVE_DIALOG').open();
										}
										
									}else{
										message = "Employee ID is required.";
										ui('EMP_ID').setValueState("Error").setValueStateText(message);
										lv_message_strip = fn_show_message_strip("MESSAGE_STRIP_EMPINFO_ERROR",message);
										ui('MESSAGE_STRIP_EMPINFO_ERROR').setVisible(true).addContent(lv_message_strip);
									}
												
                            }
                        }),
						new sap.m.Button("CREATE_EMPINFO_EDIT_BTN", {
                            visible: true,
                            icon: "sap-icon://edit",
                            press: function () {
								screenMode._edit();
                            }
                        }),
						new sap.m.Button("CREATE_EMPINFO_CANCEL_BTN", {
                            visible: true,
                            icon: "sap-icon://decline",
                            press: function () {
								screenMode._display(screenMode._id);
                            }
                        }),
						new sap.m.Button("CREATE_BP_DEL_BTN", {
                            visible: true,
                            icon: "sap-icon://delete",
                            press: function () {
								ui('BP_DELETE_DIALOG').open();
                            }
                        }),
                    ]
                }).addStyleClass('class_transparent_bar'),

			],
			content: [
                new sap.ui.layout.Grid({
                    defaultSpan:"L12 M12 S12",
					width:"auto",
					content:[
                        new sap.ui.layout.form.SimpleForm("PANEL_FORM",{
							title: "New Employee ID",
                            maxContainerCols:2,
							labelMinWidth:130,
							content:[
                                new sap.ui.core.Title("GENERAL_INFO_TITLE1",{text:""}),

								new sap.m.Label({text:"Employee ID",width:"150px"}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_ID",{
									value:"", 
									width:TextWidth,
									liveChange: function(oEvt){
										fn_livechange_numeric_input(oEvt);
									},
									change : function(oEvt){
										let lv_value = oEvt.getSource().getValue().trim();
										let label = "New Employee ID"
										let lv_empID = label + " (" + lv_value + ")";
										ui("PANEL_FORM").setTitle(lv_empID);
										
									}
								}),

								new sap.m.Label({text:"First Name",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_FNAME",{value:"", width:TextWidth}),

								new sap.m.Label({text:"Last Name",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_LNAME",{value:"", width:TextWidth}),

								new sap.m.Label({text:"Date of Birth",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.DatePicker("EMP_BDATE",{value:"", width:TextWidth}),
			
								new sap.m.Label({text:"Age",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_AGE",{
									value:"", 
									width:TextWidth,
									liveChange: function(oEvt){
										fn_livechange_numeric_input(oEvt);
									},
								}),

                                new sap.m.Label({text:"Gender",width:"160px"}).addStyleClass('class_label_padding'),
								new sap.m.Select("EMP_GENDER",{
									width:TextWidth,
									//selectedKey: "",
									items: [
										new sap.ui.core.ListItem({
											text: "MALE",
											key: "MALE",
											additionalText: "MALE",
										}),
										new sap.ui.core.ListItem({
											text: "FEMALE",
											key: "FEMALE",
											additionalText: "FEMALE",
										}),
										new sap.ui.core.ListItem({
											text: "OTHERS",
											key: "OTHERS",
											additionalText: "OTHERS",
										})
									]
								}),

                                new sap.m.Label({text:"Phone Number",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_PHONENUMBER",{
									value:"", 
									width:TextWidth,
									liveChange: function(oEvt){
										fn_livechange_numeric_input(oEvt);
									},
								}),

								new sap.m.Label({text:"Email Address",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_EMAILADD",{value:"", width:TextWidth}),

								new sap.ui.core.Title("GENERAL_INFO_TITLE2",{text:""}),

								new sap.m.Label({text:"Education Level",width:"150px"}).addStyleClass('class_label_padding'),
								new sap.m.RadioButtonGroup("EMP_EDUCLEVEL",{
									buttons: [
										new sap.m.RadioButton({
											id:"Elementary",
											text: "Elementary"
										}),
										new sap.m.RadioButton({
											id:"JuniorHighSchool",
											text: "Junior High School"
										}),
										new sap.m.RadioButton({
											id:"SeniorHighSchool",
											text: "Senior High School"
										}),
										new sap.m.RadioButton({
											id:"Tertiary",
											text: "Tertiary"
										}),
										new sap.m.RadioButton({
											id:"ALS",
											text: "Alternative Learning System"
										}),
									]
								}),
                          
								new sap.m.Label({text:"Job Title",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_JOBTITLE",{value:"", width:TextWidth}),

                                new sap.m.Label({text:"Employment Start Date",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.DatePicker("EMP_STARTDATE",{value:"", width:TextWidth}),
								
								new sap.m.Label({text:"Employment Status",width:"160px"}).addStyleClass('class_label_padding'),
								new sap.m.Select("EMP_STATUS",{
									width:TextWidth,
									//selectedKey: "",
									items: [
										new sap.ui.core.ListItem({
											text: "FULL-TIME",
											key: "FULL-TIME",
											additionalText: "FULL-TIME"
											
										}),
										new sap.ui.core.ListItem({
											text: "PART-TIME",
											key: "PART-TIME",
											additionalText: "PART-TIME"
											
										}),
										new sap.ui.core.ListItem({
											text: "TEMPORARY",
											key: "TEMPORARY",
											additionalText: "TEMPORARY"
								
										})
									]
								}),

                                new sap.m.Label({text:"Work Schedule",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_WORKSCHED",{value:"", width:TextWidth}),

								new sap.m.Label({text:"Manager's Name",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_MANAGER",{value:"", width:TextWidth}),

								new sap.m.Label({text:"Emergency Contact Information",width:labelWidth}).addStyleClass('class_label_padding'),
								new sap.m.Input("EMP_EMERGENCYCONTACTINFO",{value:"", width:TextWidth}),

                            ]
                        })
                    ]
                })
            ]
        });

        page.setCustomHeader(pageHeader);
        page.addContent(crumbs);
		page.addContent(errorPanel);
        //page.addContent(createPageFormHeader);
		page.addContent(createPageFormContent);
        return page;
    }

	function createEmpInfoDisplayPage(){
				
		var lv_Page  = new sap.m.Page("EMPINFO_PAGE_DISPLAY",{}).addStyleClass('sapUiSizeCompact');
		
		var lv_header = new sap.m.Bar({
			enableFlexBox: false,
			contentLeft:[
				new sap.m.Button({ icon:"sap-icon://nav-back",
					press:function(oEvt){
						go_App_Right.back();
					} 
				}),
				new sap.m.Button({icon:"sap-icon://menu2",
					press:function(){
						go_SplitContainer.setSecondaryContentWidth("250px");
						if(!go_SplitContainer.getShowSecondaryContent()){
							go_SplitContainer.setShowSecondaryContent(true);
						} else {							
							go_SplitContainer.setShowSecondaryContent(false);
						}
					}
				})
				//new sap.m.Image({src: logo_path}),
			],

			contentMiddle:[gv_Lbl_NewPrdPage_Title = new sap.m.Label("DISP_EMPINFO_TITLE",{text:"Display Employee's Information"})],
			
			contentRight:[
				new sap.m.Button({
					icon: "sap-icon://home",
					press: function(){
						window.location.href = MainPageLink; 
					}
				})
			]
		});
		
		var lv_crumbs = new sap.m.Breadcrumbs("DISP_EMPINFO_BRDCRMS",{
            currentLocationText: "Display Employee's Information",
			links: [
    new sap.m.Link({
        text: "Home",
        press: function(oEvt) {
            // fn_click_breadcrumbs("HOME");
        }
    }).attachPress(function() {
        window.location.href = ""; // Home button is where you will input a new employee info
    }),
]

        }).addStyleClass('breadcrumbs-padding');
		
		
		var lv_searchfield =  new sap.m.Bar({
			enableFlexBox: false,
			contentLeft: [
				// actual search field
				new sap.m.SearchField("SEARCHFIELD_DISPLAY_OUTLET",{
					width: "99%",
					liveChange: function(oEvt){
						var lv_search_val = oEvt.getSource().getValue().trim();
						if(lv_search_val == ""){
							ui("DISPLAY_EMPINFO_TABLE").setVisible(false);
						}
					},
					placeholder: "Type the ID to search the specific Employee Information.",
					search: function(oEvent){
						var lv_searchval = oEvent.getSource().getValue().trim();
						displayEmpInfo._get_data(lv_searchval);
					},
				})
			],
		});
		
		var lv_table = new sap.ui.table.Table("DISPLAY_EMPINFO_TABLE", {
			visible:false,
			visibleRowCountMode:"Auto",
			selectionMode:"None",
			enableCellFilter: true,
			enableColumnReordering:true,
			toolbar:[
				new sap.m.Toolbar({
					design:"Transparent",
					content:[
						new sap.m.Text("DISPLAY_EMPINFO_TABLE_LABEL",{text:"List (0)"}),
					]
				})
			],
			filter:function(oEvt){
				oEvt.getSource().getBinding("rows").attachChange(function(oEvt){
					var lv_row_count = oEvt.getSource().iLength;
					ui('DISPLAY_EMPINFO_TABLE_LABEL').setText("List (" + lv_row_count + ")");
				});
			},
			cellClick: function(oEvt){
				
				var lv_bind = oEvt.getParameters().rowBindingContext;
				
				if(lv_bind != undefined){
					var lv_emp_id = oEvt.getParameters().rowBindingContext.getProperty("EMP_ID");
					if(lv_emp_id){
						screenMode._display(lv_emp_id);
					}
				}
				
			},
			columns: [
			
				new sap.ui.table.Column({label:new sap.m.Text({text:"Employee ID"}),
					width:"20%",
					sortProperty:"EMP_ID",
					filterProperty:"EMP_ID",
					autoResizable:true,
					template:new sap.m.Text({text:"{EMP_ID}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"First Name"}),
					width:"40%",
					sortProperty:"FNAME",
					filterProperty:"FNAME",
					autoResizable:true,
					template:new sap.m.Text({text:"{FNAME}",tooltip:"{FNAME}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Last Name"}),
					width:"40%",
					sortProperty:"LNAME",
					filterProperty:"LNAME",
					autoResizable:true,
					template:new sap.m.Text({text:"{LNAME}",tooltip:"{LNAME}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Gender"}),
					width:"40%",
					sortProperty:"GENDER",
					filterProperty:"GENDER",
					autoResizable:true,
					template:new sap.m.Text({text:"{GENDER}",tooltip:"{GENDER}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Age"}),
					width:"40%",
					sortProperty:"AGE",
					filterProperty:"AGE",
					autoResizable:true,
					template:new sap.m.Text({text:"{AGE}",tooltip:"{AGE}",maxLines:1}),
				}),

				
			]
		});
		
		lv_Page.setCustomHeader(lv_header);
		lv_Page.addContent(lv_crumbs);
		lv_Page.addContent(lv_searchfield);
		lv_Page.addContent(lv_table);
		
		return lv_Page;
	}

	function createEmpInfoList(){

		var lv_Page  = new sap.m.Page("PAGE_EMPINFO_LISTING",{}).addStyleClass('sapUiSizeCompact');

		var lv_header = new sap.m.Bar({
			enableFlexBox: false,
			contentLeft:[
				new sap.m.Button({ icon:"sap-icon://nav-back",
					press:function(oEvt){ 
						
						go_App_Right.back();
						
					}
				}),
				new sap.m.Button({icon:"sap-icon://menu2",
					press:function(){
						go_SplitContainer.setSecondaryContentWidth("270px");
						if(!go_SplitContainer.getShowSecondaryContent()){
							go_SplitContainer.setShowSecondaryContent(true);
						} else {							
							go_SplitContainer.setShowSecondaryContent(false);
						}
					}
				}), 
				//new sap.m.Image({src: logo_path}),
				],
			contentMiddle:[gv_Lbl_NewPrdPage_Title = new sap.m.Label("EMPINFO_LISTING_PAGE_LABEL",{text:"Employee Information's Listing"})],
			
			contentRight:[
				//fn_help_button(SelectedAppID,"BP_LISTING"),
				new sap.m.Button({  
					icon: "sap-icon://home",
					press: function(){
					window.location.href = MainPageLink; 
					}
				})
			]
		});
					
		var lv_crumbs = new sap.m.Breadcrumbs("LIST_EMPINFO_BRDCRMS",{
			currentLocationText: "Employee Information's Listing",
			links: [
				new sap.m.Link({
        text: "Home",
        press: function(oEvt) {
            // fn_click_breadcrumbs("HOME");
        }
    }).attachPress(function() {
        window.location.href = ""; // Home button is where you will input a new employee info
    }),
]
		}).addStyleClass('breadcrumbs-padding');


		var lv_table = new sap.ui.table.Table("EMPINFO_LISTING_TABLE",{
			visibleRowCountMode:"Auto",
			selectionMode:"None",
			enableCellFilter: true,
			enableColumnReordering:true,
			filter:function(oEvt){
				oEvt.getSource().getBinding("rows").attachChange(function(oEvt){
					var lv_row_count = oEvt.getSource().iLength;
					ui('EMPINFO_LISTING_LABEL').setText("Employee ID (" + lv_row_count + ")");
				});
			},
			toolbar: [
                new sap.m.Toolbar({
                    content: [
                        new sap.m.Label("EMPINFO_LISTING_LABEL", {
                            text:"Employee ID (0)"
                        }),
                        new sap.m.ToolbarSpacer(),
                        new sap.m.Button("BTN_DOWNLOAD", {
                            visible: true,
                            icon: "sap-icon://download",
                            press: function () {
								if(ui('EMPINFO_LISTING_TABLE').getModel().getData().length == 0){
									fn_show_message_toast("No data to download");
								}else{
									fn_download_empinfo_listing();
								}
								
                            }
                        })
                    ]
                }).addStyleClass('class_transparent_bar'),
            ],
			cellClick: function(oEvt){
				
				var lv_bind = oEvt.getParameters().rowBindingContext;
				
				if(lv_bind != undefined){
					var lv_emp_id = oEvt.getParameters().rowBindingContext.getProperty("EMP_ID");
					if(lv_emp_id){
						screenMode._display(lv_emp_id);
					}
				}
			},
			columns:[

				// COLUMN 1
				
				new sap.ui.table.Column({label:new sap.m.Text({text:"Employee ID"}),
					width:"150px",
					sortProperty:"EMP_ID",
					filterProperty:"EMP_ID",
					//autoResizable:true,
					template:new sap.m.Text({text:"{EMP_ID}",tooltip:"{EMP_ID}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"First Name"}),
					width:"150px",
					sortProperty:"FNAME",
					filterProperty:"FNAME",
					//autoResizable:true,
					template:new sap.m.Text({text:"{FNAME}",tooltip:"{FNAME}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Last Name"}),
					width:"150px",
					sortProperty:"LNAME",
					filterProperty:"LNAME",
					template:new sap.m.Text({text:"{LNAME}",tooltip:"{LNAME}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Date of Birth"}),
					width:"150px",
					sortProperty:"BIRTHDATE",
					filterProperty:"BIRTHDATE",
					template:new sap.m.Text({text:"{BIRTHDATE}",tooltip:"{BIRTHDATE}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Age"}),
					width:"150px",
					sortProperty:"AGE",
					filterProperty:"AGE",
					template:new sap.m.Text({text:"{AGE}",tooltip:"{AGE}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Gender"}),
					width:"150px",
					sortProperty:"GENDER",
					filterProperty:"GENDER",
					template:new sap.m.Text({text:"{GENDER}",tooltip:"{GENDER}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Phone Number"}),
					width:"150px",
					sortProperty:"PHONENUM",
					filterProperty:"PHONENUM",
					template:new sap.m.Text({text:"{PHONENUM}",tooltip:"{PHONENUM}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Email Address"}),
					width:"150px",
					sortProperty:"EMAILADD",
					filterProperty:"EMAILADD",
					template:new sap.m.Text({text:"{EMAILADD}",tooltip:"{EMAILADD}",maxLines:1}),
				}),

				// COLUMN 2

				new sap.ui.table.Column({label:new sap.m.Text({text:"Education Level"}),
					width:"150px",
					sortProperty:"EDUCLVL",
					filterProperty:"EDUCLVL",
					template:new sap.m.Text({text:"{EDUCLVL}",tooltip:"{EDUCLVL}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Job Title"}),
					width:"150px",
					sortProperty:"JOBTITLE",
					filterProperty:"JOBTITLE",
					template:new sap.m.Text({text:"{JOBTITLE}",tooltip:"{JOBTITLE}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Employment Start Date"}),
					width:"170px",
					sortProperty:"EMPSTARTDATE",
					filterProperty:"EMPSTARTDATE",
					template:new sap.m.Text({text:"{EMPSTARTDATE}",tooltip:"{EMPSTARTDATE}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Employment Status"}),
					width:"150px",
					sortProperty:"EMPSTATUS",
					filterProperty:"EMPSTATUS",
					template:new sap.m.Text({text:"{EMPSTATUS}",tooltip:"{EMPSTATUS}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Work Schedule"}),
					width:"150px",
					sortProperty:"WORKSCHED",
					filterProperty:"WORKSCHED",
					template:new sap.m.Text({text:"{WORKSCHED}",tooltip:"{WORKSCHED}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Manager's Name"}),
					width:"150px",
					sortProperty:"MANAGERSNAME",
					filterProperty:"MANAGERSNAME",
					template:new sap.m.Text({text:"{MANAGERSNAME}",tooltip:"{MANAGERSNAME}",maxLines:1}),
				}),
				new sap.ui.table.Column({label:new sap.m.Text({text:"Emergency Contact Information"}),
					width:"250px",
					sortProperty:"EMERGENCYCONTACTINFO",
					filterProperty:"EMERGENCYCONTACTINFO",
					template:new sap.m.Text({text:"{EMERGENCYCONTACTINFO}",tooltip:"{EMERGENCYCONTACTINFO}",maxLines:1}),
				}),

				
			]

		});

		lv_Page.setCustomHeader(lv_header);
		lv_Page.addContent(lv_crumbs);
		lv_Page.addContent(lv_table);


		return lv_Page;
	}

</script>