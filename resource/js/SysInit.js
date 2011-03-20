/**
 * SysInit : System Common libray
 * Structure & file layout:
 * 	1,Enhancement	: for Ext JS and the other external libraries
 * 	2,Global 		: Global variable definition
 *  3,Common 		: Common definition
 *	4,Helper		: Helper function definition
 */
 
/******************************************************************************
 *  1,Enhancement Area
******************************************************************************/

/******************************************************************************
 *  2,Global Definition Area
******************************************************************************/
Ext.namespace('Ext.app');
var g_clientCache = {
	msg	: []		//global message array
};
/******************************************************************************
 *  3,Common Definition Area:
 *		- Ext.app.Module
 *		- Ext.app.App
******************************************************************************/
Ext.app.Module = function(config){
    Ext.apply(this, config);
    Ext.app.Module.superclass.constructor.call(this);
    this.init();
}

Ext.extend(Ext.app.Module, Ext.util.Observable, {
    init : Ext.emptyFn
});

Ext.app.App = function(cfg){
    Ext.apply(this, cfg);
    this.addEvents({
        'ready' : true,
        'beforeunload' : true
    });

    Ext.onReady(this.initApp, this);
};

Ext.extend(Ext.app.App, Ext.util.Observable, {
    isReady: false,
    startMenu: null,
    modules: null,

    getStartConfig : function(){

    },

    initApp : function(){
    	this.startConfig = this.startConfig || this.getStartConfig();

        this.desktop = new Ext.Desktop(this);

		this.launcher = this.desktop.taskbar.startMenu;

		this.modules = this.getModules();
        if(this.modules){
            this.initModules(this.modules);
        }

        this.init();

        Ext.EventManager.on(window, 'beforeunload', this.onUnload, this);
		this.fireEvent('ready', this);
        this.isReady = true;
    },

    getModules : Ext.emptyFn,
    init : Ext.emptyFn,

    initModules : function(ms){
		for(var i = 0, len = ms.length; i < len; i++){
            var m = ms[i];
            this.launcher.add(m.launcher);
            m.app = this;
        }
    },

    getModule : function(name){
    	var ms = this.modules;
    	for(var i = 0, len = ms.length; i < len; i++){
    		if(ms[i].id == name || ms[i].appType == name){
    			return ms[i];
			}
        }
        return '';
    },

    onReady : function(fn, scope){
        if(!this.isReady){
            this.on('ready', fn, scope);
        }else{
            fn.call(scope, this);
        }
    },

    getDesktop : function(){
        return this.desktop;
    },

    onUnload : function(e){
        if(this.fireEvent('beforeunload', this) === false){
            e.stopEvent();
        }
    }
});

/******************************************************************************
 *  4,Helper Function Definition Area
******************************************************************************/
//common function to detect object type
function isBoolean(obj)		{	return typeof obj == 'boolean';					}
function isFunction(obj)	{	return typeof obj == 'function';				}
function isNull(obj)		{	return obj === null;							}
function isNumber(obj)		{	return typeof obj == 'number' && isFinite(obj);	}
function isString(obj)		{	return typeof obj == 'string';					}
function isUndefined(obj)	{	return typeof obj == 'undefined';				}

//common function for localization
function L10N(id)	{return g_clientCache.msg[id] || id;}

//Show Exception Information
function showException(exp){
	if(isString(exp)){
		alert(exp);
	}else{
		var str = "Exception Information:\r\n";
		for(var i in exp){
			str += i.toString()+"\t: ";
			str += exp[i].toString()+"\r\n";
		}
		alert(str);
	}
}
//simple function for ajax using the same JSON format as response data
// {success:boolean,message:string,......}
//
Ext.app.simpleAjax	= {
	post : function(arrCfg){
		Ext.app.simpleAjax.config = {
			url		: '',
		    method	: 'POST',
		    scope	: this,
		    loadMask: false,
		    params	: {},
		    myCallback: Ext.emptyFn,
		    success	: Ext.app.simpleAjax.success,
			failure : Ext.app.simpleAjax.failure
		};
		Ext.apply(Ext.app.simpleAjax.config,arrCfg);
		
		if(Ext.app.simpleAjax.config.loadMask)	Ext.getBody().mask(L10N('SYS_LOADING'),'x-mask-loading');
		Ext.Ajax.request(Ext.app.simpleAjax.config);
	},
	
	success	: function(response, options){
		//unmask
		if(Ext.getBody().isMasked())	Ext.getBody().unmask();
		
		//decode json from server side
		var arrResult = null;
		try{
			arrResult = Ext.util.JSON.decode(response.responseText);
		}catch(exp){
			arrResult = null;
		}
		if(!arrResult){
			Ext.Msg.show({
				title		: L10N('SYS_CONFIRM_TITLE'),
				msg			: response.responseText,
				buttons		: Ext.Msg.OK,
				icon		: Ext.MessageBox.ERROR,
				maximizable	: true,
				minWidth	: 250,
				minWidth	: 800
			});
			return;
		}
		if(typeof(Ext.app.simpleAjax.config.myCallback)=='function'){
			Ext.app.simpleAjax.config.myCallback.apply(Ext.app.simpleAjax.config.scope,[arrResult,arrResult.success]);
		}
		
		//display message if necessary
		if(typeof(arrResult.message) == 'string' && arrResult.message.length>0){
			if(arrResult.success){
				Ext.Msg.show({
					title	: L10N('SYS_CONFIRM_TITLE'),
					msg		: arrResult.message,
					buttons	: Ext.Msg.OK,
					icon	: Ext.MessageBox.INFO,
					minWidth: 250
				});
			}else{
				Ext.Msg.show({
					title	: L10N('ERROR_TITLE'),
					msg		: arrResult.message,
					buttons	: Ext.Msg.OK,
					icon	: Ext.MessageBox.ERROR,
					minWidth: 250
				});
			}
		}
	},
	
	failure	: function(response, options){
		//unmask
		if(Ext.getBody().isMasked())	Ext.getBody().unmask();
		
		var arrResult = null;
		try{
			arrResult = Ext.util.JSON.decode(response.responseText);
		}catch(exp){
			arrResult = response.responseText;
		}
		
		if(typeof(Ext.app.simpleAjax.config.myCallback)=='function'){
			Ext.app.simpleAjax.config.myCallback.apply(Ext.app.simpleAjax.config.scope,[arrResult,false]);
		}
		
		//display error message
		Ext.Msg.show({
			title	: L10N('ERROR_TITLE'),
			msg		: arrResult,
			buttons	: Ext.Msg.OK,
			icon	: Ext.MessageBox.ERROR,
			minWidth: 250
		});
	}
};