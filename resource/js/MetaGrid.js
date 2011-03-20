Ext.ux = Ext.ux || {};
Ext.ux.grid = Ext.ux.grid || {};

/*
//fix Ext bug :Permission denied to access property 'dom' from a non-chrome context
Ext.lib.Event.resolveTextNode = Ext.isGecko ? function(node){
	if(!node){
		return;
	}
	var s = HTMLElement.prototype.toString.call(node);
	if(s == '[xpconnect wrapped native prototype]' || s == '[object XULElement]'){
		return;
	}
	return node.nodeType == 3 ? node.parentNode : node;
} : function(node){
	return node && node.nodeType == 3 ? node.parentNode : node;
};

//fix rownumber issue on paging
Ext.grid.PagedRowNumberer = function(config){
    Ext.apply(this, config);
    if(this.rowspan){
        this.renderer = this.renderer.createDelegate(this);
    }
};
*/
///////////////////////////////////////////////////////////////////////////////

Ext.grid.PagedRowNumberer.prototype = {
    header		: "",
    width		: 24,
    sortable	: false,
    fixed		: false,
    hideable	: false,
    dataIndex	: '',
    id			: 'numberer',
    rowspan		: undefined,
    
    renderer : function(v, p, record, rowIndex, colIndex, store){
        if(this.rowspan){
            p.cellAttr = 'rowspan="'+this.rowspan+'"';
        }
        var i = store.lastOptions.params.start;
        if (isNaN(i)) {
            i = 0;
        }
        i = i + rowIndex + 1;
        return Number(i).toString();
    }
};

/** 
 * Ext.ux.grid.MetaGrid Extension Class 
 * Extension of Ext.grid.EditorGridPanel to handle metaData. 
 * 
 * @author Michael LeComte 
 * @version 0.2 
 * @date Feb 2, 2009 
 * 
 * @class Ext.ux.grid.MetaGrid 
 * @extends Ext.grid.EditorGridPanel 
 */ 
Ext.ux.grid.MetaGrid = Ext.extend(Ext.grid.EditorGridPanel, {
	//using download to excel function or not
	download2Excel	: Ext.emptyFn,
	
	// whether the filters's value are modified
	bFiltersUpdated : false,
	
	//defer to load data after initComponent
	deferLoad		: true,
	
	withRowNumber	: true,		//with row number in the first column
	
	withCheckBox	: false,	//with checkbox in the first column
	
	autoScroll 		: true,		//default with scroll or not
	
    initPreview		: true,
   	
   	singleSelect	: false,

    loadMask		: true,
    /** 
     * @cfg {Boolean} true to mask the grid if there's no data to make  
     * it even more obvious that the grid is empty.  This will apply a  
     * mask to the grid's body with a message in the middle if there  
     * are zero rows - quite hard for the user to miss. 
     */ 
    maskEmpty: true, 
     
    /** 
     * key number for new records (will be adjusted by new records) 
     */ 
    newKey: -1, 
     
    paging: { 
        perPage: 25 
    }, 
     
    /** 
     * @cfg {String} primaryKey The database table primary key. 
     */ 
    primaryKey: 'id', 
     
    stripeRows: true, 

    trackMouseOver: true, 
     
    initComponent: function(){ 
/*		//fix grid filter
		Ext.ux.menu.RangeMenu.prototype.icons = {
			gt: '/javascripts/GridFilters/img/greater_then.png',
			lt: '/javascripts/GridFilters/img/less_then.png',
			eq: '/javascripts/GridFilters/img/equals.png'
		};
		Ext.ux.grid.filter.StringFilter.prototype.icon = '/javascripts/GridFilters/img/find.png';

		// Add function getActiveFiltersCount to get active filters's count.
		Ext.ux.grid.GridFilters.prototype.getActiveFiltersCount = function() {
			var nActiveCount = 0;
			this.filters.each(function(filter){
				if (filter.active) {
					++nActiveCount;
				}
			});
			
			return nActiveCount;
		};

		// Add function setValue to set value for one filter.
		Ext.ux.grid.GridFilters.prototype.setValue = function(strDataIndex, value) {
			this.filters.each(function(filter){
				if (filter.dataIndex == strDataIndex) {
					filter.setValue(value);
					filter.setActive(true, true);
					
					return false;
				} else {
		            return true;
		        }
			});
		};*/

        Ext.applyIf(this, { 
            plugins: [], 
            pagingPlugins: [], 
            // customize view config 
            viewConfig : { 
                emptyText	: 'No Data', 
                forceFit	: false,
                autoWidth	: true 
            } 
        }); 

        if (this.filters) { 
            this.filters = new Ext.ux.grid.GridFilters({ 
                filters:[] 
            });
            this.plugins.push(this.filters);
            this.pagingPlugins.push(this.filters);
        } 

        this.store = new Ext.data.Store({ 
            url: this.url, 
            // create reader (reader will be further configured through metaData sent by server) 
            reader: new Ext.data.JsonReader(), 
            baseParams: this.baseParams, 
            listeners: { 
                // register to the store's metachange event 
                metachange: { 
                    fn: this.onMetaChange, 
                    scope: this 
                }, 
                loadexception: { 
                    fn: function(proxy, options, response, e){ 
                        if (Ext.isFirebug) { 
                            console.warn('store loadexception: ', arguments); 
                        } 
                        else { 
                            Ext.Msg.alert('store loadexception: ', arguments); 
                        } 
                    } 
                }, 
                scope: this 
            } 
        }); 

        // mask the grid if there is no data if so configured 
        if (this.maskEmpty) { 
            this.store.on( 'load', function() { 
                    var el = this.getGridEl(); 
                    if (this.store.getTotalCount() == 0 && typeof el == 'object') { 
                        //el.mask('No Data', 'x-mask'); 
                        Ext.Msg.alert(CRM_MSG_ALL.ERROR_TITLE,("undefined" != typeof (CRM_MSG_ALL["REMIND_RESET_FILTERS"]))
					            								? CRM_MSG_ALL.REMIND_RESET_FILTERS 
					            								: "No data! Please click 'Reset Filters' button to reset all Filters."); 
                    } 

					var btb = this.getBottomToolbar();
		            //add reset filter button in case of using filters
                    if(this.filters){
                    	if(typeof(btb.addFilterBtn) == "undefined" ){
							btb.addSeparator();
				   			btb.addButton( new Ext.Toolbar.Button({
					   				iconCls: 'reset_password',
					   				tooltip	: ("undefined" != typeof (CRM_MSG_ALL["RESET_FILTERS"])) ? CRM_MSG_ALL.RESET_FILTERS : "Reset Filters",
					   				handler	: this.clearFilters.createDelegate(this, [this])
					   		}) );
				   			btb['addFilterBtn'] = true;
			   			}
                    }
                    if(typeof(btb.download2Excel) == "undefined" && typeof(this.download2Excel) == 'function' && Ext.emptyFn !== this.download2Excel){
						btb.addSeparator();
			   			btb.addButton( new Ext.Toolbar.Button({
		                    iconCls		: 'export2excel',
		                    tooltip		: CRM_MSG_ALL.CSS_SMSMANAGE_EXPORTEXCEL,
		                    handler		: this.download2Excel.createDelegate(this)
				   		}) );
				   		btb['download2Excel'] = true;
                    }
                    btb = null;
                }, this 
            ); 
        }  
         
        //Create Paging Toolbar       
        this.pagingToolbar = new Ext.PagingToolbar({ 
            id			: 'pager', 
            store		: this.store, 
            //pageSize: this.options.pageSize,//makes this global for all who need it 
            pageFit		: true, 
            pageSize	: this.pageSize || 25, //default is 20 
            plugins		: this.pagingPlugins, 
            displayInfo	: true,//default is false (to not show displayMsg) 
            displayMsg	: ("undefined" != typeof (CRM_MSG_ALL["PAGING_DISPLAYMSG"]))?CRM_MSG_ALL.PAGING_DISPLAYMSG:'Displaying {0} - {1} of {2}', 
            emptyMsg	: ("undefined" != typeof (CRM_MSG_ALL["PAGING_EMPTYMSG"]))?CRM_MSG_ALL.PAGING_EMPTYMSG:"No data to display",//display message when no records found 
            items		: [/*{ 
                text: 'Change data', 
                scope: this 
            }*/] 
        }); 
         
        //Add a bottom bar       
        this.bbar = this.pagingToolbar;
         
        /* 
         * JSONReader provides metachange functionality which allows you to create 
         * dynamic records natively 
         * It does not allow you to create the grid's column model dynamically. 
         */ 
        if (this.columns && (this.columns instanceof Array)) { 
            this.colModel = new Ext.grid.ColumnModel(this.columns); 
            delete this.columns; 
        } 
         
        // Create a empty colModel if none given 
        if (!this.colModel) { 
            this.colModel = new Ext.grid.ColumnModel([]); 
        } 
         
        /** 
         * defaultSortable : Boolean 
         * Default sortable of columns which have no sortable specified 
         * (defaults to false) 
         * Instead of specifying sorting permission by individual columns 
         * can just specify for entire grid 
         */ 
        this.colModel.defaultSortable = true; 
         
        Ext.ux.grid.MetaGrid.superclass.initComponent.call(this); 

		// rewrite relevent events handler
		this.on("filterupdate", this.onFilterUpdate, this);
        this.store.on("beforeload", this.onBeforeLoad, this);
    },
	
	// unselect all filters'
	clearFilters : function(grid) {
		grid.filters.clearFilters();
	},

    /** 
     * Configure the reader using the server supplied meta data. 
     * This grid is observing the store's metachange event (which will be triggered 
     * when the metaData property is detected in the returned json data object). 
     * This method is specified as the handler for the that metachange event. 
     * This method interrogates the metaData property of the json packet (passed 
     * to this method as the 2nd argument ).  The local meta property also contains 
     * other user-defined properties needed: 
     *     fields 
     *     defaultSortable 
     *     id 
     *     root 
     *     start 
     *     limit 
     *     sortinfo.field 
     *     sortinfo.direction 
     *     successProperty 
     *     totalProperty 
     * @param {Object} store 
     * @param {Object} meta The reader's meta property that exposes the JSON metadata 
     */ 
    onMetaChange: function(store, meta){ 
     
        // avoid loading meta on store reload  
        delete (store.lastOptions.params.meta); 
         
        var columns = [], editor, plugins, storeCfg, l, convert; 

		if(this.withRowNumber){
			columns.push(new Ext.grid.PagedRowNumberer());
		}

		var sm = null;
		if(this.withCheckBox){
			sm = new Ext.grid.CheckboxSelectionModel();
		}else if(this.singleSelect){
			sm = new Ext.grid.RowSelectionModel({singleSelect:this.singleSelect});
		}
		if(sm){
			columns.push(sm);
			//this.selModel = sm;
		}
		
        // set primary Key          
        this.primaryKey = meta.id; 

        // the metaData.fields property in the returned data packet will be used to: 
        // 1. internally create a Record constructor using the array of field definitions: 
        // this.recordType = Ext.data.Record.create(o.metaData.fields); 
        // both the reader and the store will have a recordType property 
        // 2. reconfigure the column model: 
        var funcNumRdr = Ext.util.Format.numberRenderer('0,000');
        Ext.each(meta.fields, function(col){ 
         
            // if plugin specified 
            if (col.plugin !== undefined) { 
                columns.push(eval(col.plugin)); 
                return; 
            } 
             
            // if header property is not specified do not add to column model 
            if (col.header == undefined) { 
                return; 
            } 
             
            // if not specified assign dataIndex = name                
            if (typeof col.dataIndex == "undefined") { 
                col.dataIndex = col.name; 
            } 
             
            //if using gridFilters extension 
            if (this.filters) { 
                if (col.filter !== undefined) { 
                    if ((col.filter.type !== undefined)) {
                    	if(col.filter.type == 'nlist' || col.filter.type == 'clist'){
                    		col.filter.type = 'list';
                    	}
                    	//set default value by filter type
                    	if(col.filter.type == 'list'){
                    		//phpMode
                        	col.filter.phpMode = col.filter.phpMode || true;
                        }else if(col.filter.type == 'numeric'){
                        	col.align 	= col.align || 'right';
                        	col.renderer= col.renderer || funcNumRdr;
                        }else if(col.filter.type == 'date'){
                        	col.align = col.align || 'center';
                        	col.width = col.width || 76;
                        }
                        
                        col.filter.dataIndex = col.dataIndex;
                        
			            // if filter.store specified in meta data 
			            if (typeof col.filter.store == "string") { 
			                // if specified Ext.util or a function will eval to get that function 
			                if(col.filter.store.indexOf("Ext") > 0 || col.filter.store.indexOf("function") > 0) { 
			                	col.filter.store = eval(col.filter.store);
			                }else if(typeof(window[col.filter.store]) == 'function'){
			                	col.filter.store = window[col.filter.store].createDelegate(this);
			                }else if(typeof(this[col.filter.store]) == 'function'){
			                	col.filter.store = this[col.filter.store].createDelegate(this);
			                }else{
								var objApp = new CRM.App();
								col.filter.store = objApp.getDictStore(col.filter.store);
			                }
			            }

                        this.filters.addFilter(col.filter); 
                    }
                } 
                delete col.filter; 
            } 
             
            // if renderer specified in meta data 
            if (typeof col.renderer == "string") {
                // if specified Ext.util or a function will eval to get that function 
                if (col.renderer.indexOf("Ext") < 0 && col.renderer.indexOf("function") < 0) { 
                    col.renderer = this[col.renderer].createDelegate(this); 
                } 
                else { 
                    col.renderer = eval(col.renderer); 
                } 
            } 
             
            /* 
             // if want to modify default column id 
             if(typeof col.id == "undefined"){ 
             col.id = 'c' + i; 
             } 
             */ 
            // if listeners specified in meta data 
            l = col.listeners; 
            if (typeof l == "object") { 
                for (var e in l) { 
                    if (typeof e == "string") { 
                        for (var c in l[e]) { 
                            if (typeof c == "string") { 
                                l[e][c] = eval(l[e][c]); 
                            } 
                        } 
                    } 
                } 
            } 
             
            // if convert specified assume it's a function and eval it 
            if (col.convert) { 
                col.convert = eval(col.convert); 
            } 

            editor = col.editor; 
             
            if (editor) { 
             
                switch (editor.xtype) { 
                    case 'checkbox': 
                        delete (col.editor); 
                        delete (col.renderer); 
                        col = new Ext.grid.CheckColumn(col); 
                        col.editor = Ext.ComponentMgr.create(editor, 'textfield'); 
                        col.init(this); 
                        break; 
                    case 'combo': 
                        if (col.editor.store) { 
                            storeCfg = col.editor.store; 
                            col.editor.store = new Ext.data[storeCfg.storeType](storeCfg.config);
                        } 
                        col.editor = Ext.ComponentMgr.create(editor, 'textfield'); 
                        break; 
                    case 'datefield': 
                        col.editor = Ext.ComponentMgr.create(editor, 'textfield'); 
                        break; 
                    default: 
                        col.editor = Ext.ComponentMgr.create(editor, 'textfield'); 
                        break; 
                } 
                 
                plugins = editor.plugins; 
                delete (editor.plugins); 
                 
                //configure any listeners specified for this column's editor 
                l = editor.listeners; 
                if (typeof l == "object") { 
                    for (var e in l) { 
                        if (typeof e == "string") { 
                            for (var c in l[e]) { 
                                if (typeof c == "string") { 
                                    l[e][c] = eval(l[e][c]); 
                                } 
                            } 
                        } 
                    } 
                } 
            } 
             
            if (plugins instanceof Array) { 
                editor.plugins = []; 
                Ext.each(plugins, function(plugin){ 
                    plugin.name = plugin.name || col.dataIndex; 
                    editor.plugins.push(Ext.ComponentMgr.create(plugin)); 
                }); 
            } 
             
            // add column to colModel config array             
            columns.push(col); 
             
        }, this); // end of columns loop         
        var cm = new Ext.grid.ColumnModel(columns); 
         
        if (meta.defaultSortable != undefined) { 
            cm.defaultSortable = meta.defaultSortable; 
        } 
         
        // can change the store if we need to also, perhaps if we detect a groupField 
        // config for example 
        // meta.groupField or meta.storeCfg.groupField; 
        var store = this.store;  
         
        // Reconfigure the grid to use a different Store and Column Model. The View  
        // will be bound to the new objects and refreshed. 
        var view = this.getView();
        view.init(this);
        this.reconfigure(store, cm); 

        // to add a record, just get a reference to the recordType: 
        // this.store.recordType 
        // and then use it to create a new record: 
        // var r = new s.recordType({ 
        //     value:4, 
        //     displayField:"Last Week", 
        //     total: 23 
        // }); 
        // and then insert it into the store (updates the grid visually also): 
        // this.store.insert(0, r); 
         
        //update the store for the pagingtoolbar also 
        if(typeof(this.pagingToolbar) != 'undefined'){
	        var oldStore = this.pagingToolbar.store; 
	        this.pagingToolbar.unbind(oldStore); 
	        this.pagingToolbar.bind(store); 
        }
        if (this.stateful) { 
            this.initState(); 
        }
         /*
        if (!this.view.hmenu.items.containsKey('reset')) { 
            this.view.hmenu.add({ 
                id: "reset", 
                text: "Reset Columns", 
                cls: "xg-hmenu-reset-columns" 
            }); 
        } */
    },
    
	// for filters
	onFilterUpdate : function(filters, filter) {
		this.bFiltersUpdated = true;
	},
	
	// set params before load
	onBeforeLoad : function(s, options) {
		if (this.bFiltersUpdated) {
			options.params.start = 0;
			this.bFiltersUpdated = false;
		}
	}
});

Ext.reg('meta-grid', Ext.ux.grid.MetaGrid);