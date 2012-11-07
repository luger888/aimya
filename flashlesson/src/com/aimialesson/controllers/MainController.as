package com.aimialesson.controllers
{
	import com.aimialesson.UI.views.MainUI;
	import com.aimialesson.events.*;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Presentation;
	
	import flash.events.EventDispatcher;

	[Event (name="connectInitComplete", type="com.aimialesson.events.AppEvent")]
	public class MainController extends EventDispatcher
	{
		private var mediaController:MediaController;
		private var streamController:StreamController;
		private var presentationController:PresentationController;
		private var soController:SharedObjectController;
		private var recorderController:RecorderController;
		private var mainUI:MainUI;
		public function MainController()
		{
		}
		
		public function init ( mainUI : MainUI, parameters : Object ) : void {
			debug("MainController:init");
			mediaController = new MediaController();
			mediaController.setParameters(parameters);
			mediaController.addEventListener(AppEvent.CONNECT_INIT_COMPLETE, appNetConnectHandler);
			mediaController.initConnection();
			presentationController = new PresentationController();
			soController = new SharedObjectController();
			soController.initSO();
			this.mainUI = mainUI;
			recorderController = new RecorderController();
			recorderController.init(mainUI);
			recorderController.startTransferring();
		}
		
		//event handling
		public function onPresentationEvent ( event : PresentationEvent ) : void {
			debug("MainController:onPresentationEvent");
			presentationController.presentationEventHandler(event);
			soController.setSOProperty("imageN",Presentation.getInstance().currentImageNumber.toString());
		}
		
		public function onTextChatEvent ( event : TextChatEvent ) : void {
			debug("MainController:onTextChatEvent");
			soController.setSOProperty("chatMessage", event.value);
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