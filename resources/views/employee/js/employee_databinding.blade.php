<script>
	var EmpInfoData = [

	];

	const EmpInfoDataOrganizer = {
		_getEmpData : async function(){
			const response =  await fetch("/employee/getEmpData");
			const data = await response.json();
			return data;
		},
		_filteredById : async function(id){
			let busyDialog = showBusyDialog("Please wait loading...");
			busyDialog.open();
			const emp_id = btoa(id);
			const response =  await fetch("/employee/getDataById/"+emp_id);
			const getDataById = await response.json();
			busyDialog.close();
			return getDataById;
		},
		_updateById : function(id){
			let busyDialog = showBusyDialog("Please wait loading...");
				busyDialog.open();
			
				let EmpDataUpdate = [{
						EMP_ID      				: ui('EMP_ID').getValue().trim(),
						FNAME    					: ui('EMP_FNAME').getValue().trim(),
						LNAME    					: ui('EMP_LNAME').getValue().trim(),
						BIRTHDATE    				: ui('EMP_BDATE').getValue().trim(),
						AGE    						: ui('EMP_AGE').getValue().trim(),
						GENDER     					: ui('EMP_GENDER').getSelectedKey(),
						PHONENUM    				: ui('EMP_PHONENUMBER').getValue().trim(),
						EMAILADD    				: ui('EMP_EMAILADD').getValue().trim(),

						EDUCLVL						: ui("EMP_EDUCLEVEL").getSelectedButton().getId(),
						JOBTITLE    				: ui('EMP_JOBTITLE').getValue().trim(),
						EMPSTARTDATE    			: ui('EMP_STARTDATE').getValue().trim(),
						EMPSTATUS     				: ui('EMP_STATUS').getSelectedKey(),
						WORKSCHED    				: ui('EMP_WORKSCHED').getValue().trim(),
						MANAGERSNAME      			: ui('EMP_MANAGER').getValue().trim(),
						EMERGENCYCONTACTINFO     	: ui('EMP_EMERGENCYCONTACTINFO').getValue().trim()
				}];

				const data = {
					EMP_ID : id,	
					EmpDataUpdate : EmpDataUpdate
				}
				fetch('/employee/update_data',{
				method : 'POST',
				headers : {
					'Content-Type' : 'application/json',
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				body : JSON.stringify(data)
			}).then((response) => {
				if(response.ok){
					screenMode._display(id);
					fn_show_message_toast("Successfully updated employee information #"+id);
				}
				console.log(response);
				return response.json();
			}).then(data => {
				console.log(data);
				busyDialog.close();
			}).catch((err) => {
				console.log("Rejected "+err);
				busyDialog.close();
			});
		},
		_removeById : function(id){
			let busyDialog = showBusyDialog("Please wait loading...");
				busyDialog.open();
				
			const emp_id = {EMP_ID : id}
			fetch('/employee/removeDataById',{
				method : 'POST',
				headers : {
					'Content-Type' : 'application/json',
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				body : JSON.stringify(emp_id)
			}).then((response) => {
				if(response.ok){
					fn_show_message_toast("Successfully deleted employee information #"+id);
					ui('LEFT_MENU_TEMPLATE-MENU_LIST-2').firePress();
				}
				console.log(response);
				return response.json();
			}).then(data => {
				console.log(data);
				busyDialog.close();
			}).catch((err) => {
				console.log("Rejected "+err);
				busyDialog.close();
			});
		},
		_getRadioIndex : function(id){
			let radioButton = ui("EMP_EDUCLEVEL").getButtons();
			let selectedIndex;
			for(let i=0; i<radioButton.length; i++){
				if(radioButton[i].getId() == id){
					selectedIndex = i;
				}
			}

			return selectedIndex;

		},
		_validateEmpID : function(id){
			let isExist = false;
			for(let i=0; i<EmpInfoData.length; i++){
				if(EmpInfoData[i].empID == id){
					isExist = true;
					break;
				}
			}
			return isExist;
		}
	}

	const screenMode = {
		_id : "",
		_title : "",
		_mode : "",
		_create : function(){
			let empid = Math.floor(Math.random()*1000000);	
			ui("EMP_ID").setValue(empid);

			this._mode = "create";
			let empinfo_title = this._title;
			empinfo_title = "Create A New Employee Information";
			this._clear();
			//Buttons
			ui("CREATE_EMPINFO_SAVE_BTN").setVisible(true);
			ui("CREATE_EMPINFO_EDIT_BTN").setVisible(false);
			ui("CREATE_EMPINFO_CANCEL_BTN").setVisible(false);
			ui("CREATE_EMPINFO_DEL_BTN").setVisible(false);


			//title and crumbs
			ui('EMPINFO_TITLE').setText(empinfo_title);
			ui('CREATE_EMPINFO_BRDCRMS').setCurrentLocationText(empinfo_title);
			ui("PANEL_FORM").setTitle("New Employee Information");

			//Fields
			ui('EMP_ID').setEditable(false);
			ui('EMP_FNAME').setEditable(true);
			ui('EMP_LNAME').setEditable(true);
			ui('EMP_BDATE').setEditable(true);
			ui('EMP_AGE').setEditable(true);
			ui('EMP_GENDER').setEditable(true);
			ui('EMP_PHONENUMBER').setEditable(true);
			ui('EMP_EMAILADD').setEditable(true);

			ui('EMP_EDUCLEVEL').setEditable(true);
			ui('EMP_JOBTITLE').setEditable(true);
			ui('EMP_STARTDATE').setEditable(true);
			ui('EMP_STATUS').setEditable(true);
			ui('EMP_WORKSCHED').setEditable(true);
			ui('EMP_MANAGER').setEditable(true);
			ui('EMP_EMERGENCYCONTACTINFO').setEditable(true);

			go_App_Right.to('CREATE_EMPINFO_PAGE');
		},
		_edit : function(){
			this._mode = "edit";
			//Buttons
			ui("CREATE_EMPINFO_SAVE_BTN").setVisible(true);
			ui("CREATE_EMPINFO_EDIT_BTN").setVisible(false);
			ui("CREATE_EMPINFO_CANCEL_BTN").setVisible(true);
			ui("CREATE_EMPINFO_DEL_BTN").setVisible(false);


			//Fields
			ui('EMP_ID').setEditable(false);
			ui('EMP_FNAME').setEditable(true);
			ui('EMP_LNAME').setEditable(true);
			ui('EMP_BDATE').setEditable(true);
			ui('EMP_AGE').setEditable(true);
			ui('EMP_GENDER').setEditable(true);
			ui('EMP_PHONENUMBER').setEditable(true);
			ui('EMP_EMAILADD').setEditable(true);

			ui('EMP_EDUCLEVEL').setEditable(true);
			ui('EMP_JOBTITLE').setEditable(true);
			ui('EMP_STARTDATE').setEditable(true);
			ui('EMP_STATUS').setEditable(true);
			ui('EMP_WORKSCHED').setEditable(true);
			ui('EMP_MANAGER').setEditable(true);
			ui('EMP_EMERGENCYCONTACTINFO').setEditable(true);

		},
		_display : function(id){
			ui('MESSAGE_STRIP_EMPINFO_ERROR').destroyContent().setVisible(false);
			ui('EMP_ID').setValueState("None").setValueStateText("");
			this._mode = "display";
			this._id = id;
			let empinfo_title = this._title;
			empinfo_title = "Display Employee's Information";
			//Buttons
			ui("CREATE_EMPINFO_SAVE_BTN").setVisible(false);
			ui("CREATE_EMPINFO_EDIT_BTN").setVisible(true);
			ui("CREATE_EMPINFO_CANCEL_BTN").setVisible(false);
			ui("CREATE_EMPINFO_DEL_BTN").setVisible(true);



			//fields with value
				let response =  async () => {
				let data = await EmpInfoDataOrganizer._filteredById(id);
				console.log(data);
				if(data.length > 0){
				ui('EMP_ID').setValue(data[0].EMP_ID).setEditable(false);
				ui('EMP_FNAME').setValue(data[0].FNAME).setEditable(false);
				ui('EMP_LNAME').setValue(data[0].LNAME).setEditable(false);
				ui('EMP_BDATE').setValue(data[0].BIRTHDATE).setEditable(false);
				ui('EMP_AGE').setValue(data[0].AGE).setEditable(false);
				ui('EMP_GENDER').setSelectedKey(data[0].GENDER).setEditable(false);
				ui('EMP_PHONENUMBER').setValue(data[0].PHONENUM).setEditable(false);
				ui('EMP_EMAILADD').setValue(data[0].EMAILADD).setEditable(false);

				let radioIndex = EmpInfoDataOrganizer._getRadioIndex(data[0].EDUCLVL);
				ui('EMP_EDUCLEVEL').setSelectedIndex(radioIndex).setEditable(false);

				ui('EMP_JOBTITLE').setValue(data[0].JOBTITLE).setEditable(false);
				ui('EMP_STARTDATE').setValue(data[0].EMPSTARTDATE).setEditable(false);
				ui('EMP_STATUS').setSelectedKey(data[0].EMPSTATUS).setEditable(false);
       			ui('EMP_WORKSCHED').setValue(data[0].WORKSCHED).setEditable(false);
				ui('EMP_MANAGER').setValue(data[0].MANAGERSNAME).setEditable(false);
				ui('EMP_EMERGENCYCONTACTINFO').setValue(data[0].EMERGENCYCONTACTINFO).setEditable(false);

				//title and crumbs
				ui('EMPINFO_TITLE').setText(empinfo_title);
				ui('CREATE_EMPINFO_BRDCRMS').setCurrentLocationText(empinfo_title);
				ui("PANEL_FORM").setTitle("Employee ID "+"("+data[0].EMP_ID+")");

				go_App_Right.to('CREATE_EMPINFO_PAGE');
				}	
			};
			response();			
		},
		_clear : function(){
			ui('MESSAGE_STRIP_EMPINFO_ERROR').destroyContent().setVisible(false);
			// ui('EMP_ID').setValue("");

			ui('EMP_FNAME').setValue("");
			ui('EMP_LNAME').setValue("");
			ui('EMP_BDATE').setValue("");
			ui('EMP_AGE').setValue("");
			ui('EMP_GENDER').setValue("");
			ui('EMP_PHONENUMBER').setValue("");
			ui('EMP_EMAILADD').setValue("");

			ui('EMP_EDUCLEVEL').setSelectedIndex(0).setEditable(true);
			ui('EMP_JOBTITLE').setValue("");
			ui('EMP_STARTDATE').setValue("");
			ui('EMP_STATUS').setValue("");
			ui('EMP_WORKSCHED').setValue("");
			ui('EMP_MANAGER').setValue("");
			ui('EMP_EMERGENCYCONTACTINFO').setValue("");

		}
	};

    const createEmpInfo = () => {
		let busyDialog = showBusyDialog("Please wait loading...");
		busyDialog.open();
		let data_for_general = {
			EMP_ID      				: ui('EMP_ID').getValue().trim(),
			FNAME    					: ui('EMP_FNAME').getValue().trim(),
			LNAME    					: ui('EMP_LNAME').getValue().trim(),
			BIRTHDATE    				: ui('EMP_BDATE').getValue().trim(),
			AGE      					: ui('EMP_AGE').getValue().trim(),
			GENDER     					: ui('EMP_GENDER').getSelectedKey(), 
			PHONENUM		   			: ui('EMP_PHONENUMBER').getValue().trim(),
			EMAILADD      				: ui('EMP_EMAILADD').getValue().trim(),
			EDUCLVL						: ui("EMP_EDUCLEVEL").getSelectedButton().getId(),
			JOBTITLE     				: ui('EMP_JOBTITLE').getValue().trim(),
			EMPSTARTDATE     	 	    : ui('EMP_STARTDATE').getValue().trim(),
			EMPSTATUS     	 		    : ui('EMP_STATUS').getSelectedKey(),
			WORKSCHED     				: ui('EMP_WORKSCHED').getValue().trim(),
			MANAGERSNAME     			: ui('EMP_MANAGER').getValue().trim(),
			EMERGENCYCONTACTINFO   	    : ui('EMP_EMERGENCYCONTACTINFO').getValue().trim(),
   		};
		
		//commented use for backend only

		fetch('/employee/create_data',{
			method : 'POST',
			headers : {
				'Content-Type' : 'application/json',
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			body : JSON.stringify(data_for_general)
		}).then((response) => {
			if(response.ok){
				screenMode._display(data_for_general.EMP_ID);
				fn_show_message_toast("Successfully created new employee #"+data_for_general.EMP_ID);
			}
			console.log(response);
			return response.json();
		}).then(data => {
			console.log(data);
			busyDialog.close();
		}).catch((err) => {
			console.log("Rejected "+err);
			busyDialog.close();
		});
        
    }

	const displayEmpInfo =  {
		
		_get_data:  async (search) =>{
			
			let busyDialog = showBusyDialog("Please wait loading...");
				busyDialog.open();

				let data = await EmpInfoDataOrganizer._filteredById(search);
				displayEmpInfo._bind_data(data);
			
			
			setTimeout(() => {busyDialog.close();}, 2000);
		},
		_bind_data(data){
		
			ui("DISPLAY_EMPINFO_TABLE").unbindRows();
			
			var lt_model = new sap.ui.model.json.JSONModel();
				lt_model.setSizeLimit(data.length);
				lt_model.setData(data);
				
			ui('DISPLAY_EMPINFO_TABLE').setModel(lt_model).bindRows("/");
			ui("DISPLAY_EMPINFO_TABLE").setVisible(true);
			
			ui('DISPLAY_EMPINFO_TABLE_LABEL').setText("List (" + data.length + ")");
			//fn_clear_table_sorter("DISPLAY_EMPINFO_TABLE");
			
		}		
	};

	const EmpInfolisting = {
		_getData : function(data){
			ui("EMPINFO_LISTING_TABLE").unbindRows();
			
			var lt_model = new sap.ui.model.json.JSONModel();
				lt_model.setSizeLimit(data.length);
				lt_model.setData(data);
				
			ui('EMPINFO_LISTING_TABLE').setModel(lt_model).bindRows("/");
			ui("EMPINFO_LISTING_TABLE").setVisible(true);
			
			ui('EMPINFO_LISTING_LABEL').setText("Employee's Data (" + data.length + ")");
		}
	}

	let lv_dialog_save = new sap.m.Dialog("EMPINFO_SAVE_DIALOG",{
		title: "Confirmation",
		beginButton: new sap.m.Button({
			text:"Okay",
			type:"Accept",
			icon:"sap-icon://accept",
			press:function(oEvt){
				if(screenMode._mode == "create"){
					createEmpInfo();
				}else{
					EmpInfoDataOrganizer._updateById(screenMode._id);
				}

				oEvt.getSource().getParent().close();
			}
		}),
		endButton:new sap.m.Button({
			text:"Cancel",
			type:"Reject",
			icon:"sap-icon://decline",
			press:function(oEvt){
			oEvt.getSource().getParent().close();
			}
		}),
		content:[
			new sap.m.HBox({
				items:[
				new sap.m.Label({text:"Confirm to add new employee?"})
				]
			})
		]
	}).addStyleClass('sapUiSizeCompact');
	let lv_dialog_del = new sap.m.Dialog("EMPINFO_DELETE_DIALOG",{
		title: "Confirmation",
		beginButton: new sap.m.Button({
			text:"Ok",
			type:"Accept",
			icon:"sap-icon://accept",
			press:function(oEvt){
				EmpInfoDataOrganizer._removeById(screenMode._id);
				oEvt.getSource().getParent().close();
			}
		}),
		endButton:new sap.m.Button({
			text:"Cancel",
			type:"Reject",
			icon:"sap-icon://decline",
			press:function(oEvt){
			oEvt.getSource().getParent().close();
			}
		}),
		content:[
			new sap.m.HBox({
				items:[
				new sap.m.Label({text:"Confirm to delete employee information?"})
				]
			})
		]
	}).addStyleClass('sapUiSizeCompact');

</script>
