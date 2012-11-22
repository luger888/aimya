package com.aimialesson.controllers
{
	import com.aimialesson.UI.views.MainUI;
	import com.aimialesson.events.*;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Presentation;
	
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayCollection;

	[Event (name="connectInitComplete", type="com.aimialesson.events.AppEvent")]
	[Event (name="sharedPresentationUploaded", type="com.aimialesson.events.SharedObjectEvent")]
	public class MainController extends EventDispatcher
	{
		private var mediaController:MediaController;
		private var streamController:StreamController;
		private var presentationController:PresentationController;
		private var soController:SharedObjectController;
		private var recorderController:RecorderController;
		private var userController:UserController;
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
			userController = new UserController();
			userController.setParameters(parameters);
			presentationController = new PresentationController();
			soController = new SharedObjectController();
			soController.addEventListener(SharedObjectEvent.SHARED_PRESENTATION_UPLOADED, onSharedObjectEvent);
			soController.initSO();
			this.mainUI = mainUI;
			/*recorderController = new RecorderController();
			recorderController.init(mainUI);
			recorderController.startTransferring();*/
		}
		
		//event handling
		public function onPresentationEvent ( event : PresentationEvent ) : void {
			debug("MainController:onPresentationEvent");
			presentationController.presentationEventHandler(event);
			switch (event.type){
				case (PresentationEvent.MOVE_TO_LEFT):
				case (PresentationEvent.MOVE_TO_RIGHT): 		soController.setSOProperty("imageN",Presentation.getInstance().currentImageNumber.toString());
																break;
				case (PresentationEvent.PRESENTATION_UPLOADED): soController.setSOProperty("uploaded","true");
																break;
					
			}
		}
		
		public function onTextChatEvent ( event : NotesEvent ) : void {
			debug("MainController:onTextChatEvent");
			soController.setSOProperty("chatMessageData", event.value);
		}
		
		public function onServiceEvent ( event : ServiceEvent ) : void {
			debug("MainController:onServiceEvent");
			switch (event.type) {
				case (ServiceEvent.GET_PRESENTATION_IMAGES_RESULT) : presentationController.setImages(event.value as ArrayCollection);
																break;
			}
		}
		
		public function onSharedObjectEvent ( event : SharedObjectEvent ) : void {
			debug("MainController:onSOEvent");
			dispatchEvent(event);
			/*switch (event.type) {
				case (SOEvent.SHARED_PRESENTATION_UPLOADED) : 	presentationController.setImages(event.value as ArrayCollection);
																break;
			}*/
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