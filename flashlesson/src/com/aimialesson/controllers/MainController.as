package com.aimialesson.controllers
{
	import com.aimialesson.events.AppEvent;
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	
	import flash.events.EventDispatcher;

	[Event (name="connectInitComplete", type="com.aimialesson.events.AppEvent")]
	public class MainController extends EventDispatcher
	{
		private var mediaController:MediaController;
		private var streamController:StreamController;
		private var presentationController:PresentationController;
		public function MainController()
		{
		}
		
		public function init() : void {
			debug("MainController:init");
			mediaController = new MediaController();
			mediaController.addEventListener(AppEvent.CONNECT_INIT_COMPLETE, appNetConnectHandler);
			mediaController.initConnection();
			presentationController = new PresentationController();
		}
		
		//event handling
		public function onPresentationEvent ( event : PresentationEvent ) : void {
			
			presentationController.presentationEventHandler(event);
		}
		
		private function appNetConnectHandler ( event : AppEvent ) : void {
			debug("MainController:appNetConnectHandler");
			streamController = new StreamController();
			//streamController.addEventListener(AppEvent.MY_STREAM_INIT_COMPLETE, onMyStreamInitComplete);
			streamController.initMyNetStream();
			streamController.initPartnerNetStream();
			this.dispatchEvent( event );
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}