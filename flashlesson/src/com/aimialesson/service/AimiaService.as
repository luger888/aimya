package com.aimialesson.service
{
	//import com.adobe.serialization.json.JSON;
	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.User;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.TimerEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.utils.Timer;
	
	import mx.collections.ArrayCollection;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.http.HTTPService;
	
	public class AimiaService extends EventDispatcher
	{
		
		private var aimiaService:HTTPService;
		private const REQUEST_DELAY:int = 1000;
		private var timer:Timer = new Timer(REQUEST_DELAY);
		private var urlLoader:URLLoader = new URLLoader();
		protected var callUrl:String; 
		protected var params:Object; 
		protected var isPermanent:Boolean = false;
		
		public function AimiaService(target:IEventDispatcher=null)
		{
			super(target);
			params = new Object();
			if (User.getInstance().sessionID){
				params.PHPSESSID = User.getInstance().sessionID;
			}
		}
		
		public function makeCall():void{
			debug ("AimiaService:makeCall:" + callUrl);
			aimiaService = new HTTPService();
			aimiaService.request = params;
			aimiaService.method = URLRequestMethod.POST;
			aimiaService.url = callUrl;
			aimiaService.headers['X-Requested-With'] = "XMLHttpRequest";
			aimiaService.addEventListener(ResultEvent.RESULT, aimiaService_resultHandler);
			aimiaService.addEventListener(FaultEvent.FAULT, aimiaService_faultHandler);
			timer.addEventListener(TimerEvent.TIMER,aimiaService.send);
			aimiaService.send();
		} 
		
		protected function onSuccess ( result : Object ) : void {
		}
		
		protected function aimiaService_resultHandler ( event : ResultEvent ) : void
		{
			debug ("aimiaService_resultHandler:");
			var result:Object = JSON.parse(event.result as String);
			debug (result.answer);
			
			switch (result.answer){
				case ("success"): 	onSuccess(result);
									if (!isPermanent)timer.removeEventListener(TimerEvent.TIMER,aimiaService.send);
									break;
				case ("error")	: 	timer.removeEventListener(TimerEvent.TIMER,aimiaService.send);
									break;
			}
		}
		
		
		protected function aimiaService_faultHandler ( event : FaultEvent ) : void
		{
			debug ("aimiaService_faultHandler:" + event.fault.faultString);
		}
		
		private function onComplete ( event : Event ) : void {
			debug ("onComplete");
		}
		
		protected function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
			trace(str);
		}
	}
}