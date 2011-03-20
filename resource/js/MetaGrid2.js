//extend
Ext.ux.grid.MetaGrid2 = Ext.extend(Ext.ux.grid.MetaGrid, {
    initComponent  : function() {
        Ext.ux.grid.MetaGrid2.superclass.initComponent.call(this);
    }
});

Ext.reg('MetaGrid2', Ext.ux.grid.MetaGrid2);
/*****************************************************************************/
Ext.ux.grid.AcctMoveGrid = Ext.extend(Ext.ux.grid.MetaGrid, {
	resource : {
		//Note : every instance need to provide the following information
		id_el	: '',
		id_from	: '',
		id_to	: '',
		id_qry	: '',
		url_grid: ''
	},
	def_col		: 'N_ROWNO',//default sort by
	def_dir		: 'ASC',	//default sort direction
	def_length	: 92,		//default length of the time range
	max_length	: 92,		//max length of the time range
	open_cust	: true,
    initComponent  : function() {
    	//default config
    	var defConfig = {
			filters		: true,
			sm			: new Ext.grid.RowSelectionModel({singleSelect:true}),
			border		: true,
			baseParams	: null,
			title		: CRM_MSG_ALL.N_INOUT_HIST,
			el			: this.resource.id_el,
			layout		: 'fit', 
			url			: this.resource.url_grid,
			closeable	: false,
			deferLoad	: true,
			tbar		: new Ext.Toolbar([
				' ', CRM_MSG_ALL.SP_GRID_FROM_DATE, {
					id		: this.resource.id_from,
					xtype	: "datefield", 
					format	: "Y-m-d"
				}, " ", CRM_MSG_ALL.SP_GRID_TO_DATE , " ", {
					id		: this.resource.id_to,
					xtype	: "datefield", 
					format: "Y-m-d"
				}, " ",{
					id			: this.resource.id_qry,
					xtype		: "tbbutton", 
					iconCls		: 'query',
					text		: CRM_MSG_ALL.SYS_REPORT_BUTTONTEXT,
					handler		: this.doSearchACMov.createDelegate(this)
				},"-","->",
				'<span class="x-form-text text_center" style="background-color:white;color:red;">'+CRM_MSG_ALL.ACMOV_TIPS+'</span>',
				" "
			])
    	};
		Ext.apply(this, defConfig);
		
		Ext.ux.grid.AcctMoveGrid.superclass.initComponent.call(this);
		
    	this.store.remoteSort	= true;
    	this.store.sortInfo		= {
    		field: this.def_col, 
    		direction: this.def_dir
    	};
    	
   		this.on('activate',this.onActivate.createDelegate(this));
    	this.store.on('beforeload',this.onBeforeLoad.createDelegate(this));
    	
    	if(this.open_cust){
			this.on('rowdblclick', function(g, rowIndex, e){
				var objRecord = this.store.getAt(rowIndex);

				var strCustId = String.leftPad(objRecord.get('N_UID'), 7, '0');
				var strName = strCustId;
				if (!isNull(objRecord.get("C_NAME_CN")) && ("" != objRecord.get("C_NAME_CN").trim())) {
	   				strName = objRecord.get("C_NAME_CN");
	   			} else if (!isNull(objRecord.get("C_NAME_EN")) && ("" != objRecord.get("C_NAME_EN").trim())) {
	   				strName = objRecord.get("C_NAME_EN");
	   			}
				var strSex		= objRecord.get('C_SEX');
				var strStaff	= objRecord.get('F_STAFF');
				if(typeof(addCustomerTab) == 'function'){
					addCustomerTab(strCustId, strStaff, strName, strSex, strUserRoleName, strArmAcct);
				}
			},this);
		}
    },
    
    doSearchACMov : function(){
		var params = { 							//this is only parameters for the FIRST page load, 
            reconfigure		: true, 
            start			: 0, 					//pass start/limit parameters for paging 
            limit			: this.paging.perPage
        };
        this.store.load({params: params});
    },
    
    onActivate : function(p){
    	var objBegin = Ext.getCmp(this.resource.id_from);
    	if(objBegin && objBegin.getValue().length<=0){
    		var dtFrom = new Date();
    		dtFrom.setDate(dtFrom.getDate() - this.def_length);
    		objBegin.setValue(Ext.util.Format.date(dtFrom,'Y-m-d'));
    	}
    	
    	var objEnd = Ext.getCmp(this.resource.id_to);
    	if(objEnd && objEnd.getValue().length<=0){
    		objEnd.setValue(Ext.util.Format.date(new Date(),'Y-m-d'));
    	}
    	
    	if(!this.deferLoad)	this.doSearchACMov();
    },
    
    onBeforeLoad : function(){
		var arrTmp = {
			D_FROM		: '',
            D_TO		: ''
        };
    	var objBegin = Ext.getCmp(this.resource.id_from);
    	if(objBegin)	arrTmp.D_FROM = Ext.util.Format.date(objBegin.getValue(),'Y-m-d');
    	
    	var objEnd = Ext.getCmp(this.resource.id_to);
    	if(objEnd)		arrTmp.D_TO = Ext.util.Format.date(objEnd.getValue(),'Y-m-d');

    	if(arrTmp.D_TO < arrTmp.D_FROM){
    		Ext.Msg.show({
			   title	: CRM_MSG_ALL.ERROR_TITLE,
			   msg		: CRM_MSG_ALL.GRID_MSG_DATE_ERROR,
			   buttons	: Ext.Msg.OK,
			   icon		: Ext.MessageBox.ERROR
			});
    		return false;
    	}
    	
    	if(objBegin && objEnd && objEnd.getValue().getTime() - objBegin.getValue().getTime() > this.max_length * 24 * 3600 * 1000 ){
    		Ext.Msg.show({
			   title	: CRM_MSG_ALL.ERROR_TITLE,
			   msg		: CRM_MSG_ALL.ACMOV_QRY_RANGE01+this.max_length.toString() +CRM_MSG_ALL.ACMOV_QRY_RANGE02,
			   buttons	: Ext.Msg.OK,
			   icon		: Ext.MessageBox.ERROR
			});
    		return false;
    	}
    	
		this.store.baseParams = Ext.apply(this.store.baseParams || {}, arrTmp);
		return true;
    }
});
