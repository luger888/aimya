package com.aimialesson.controllers
{
	import com.aimialesson.events.*;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.Notes;
	import com.aimialesson.model.Presentation;
	import com.aimialesson.model.User;
	
	import flash.events.EventDispatcher;
	
	import mx.collections.ArrayCollection;

	[Event (name="timeIsOut", type="com.aimialesson.events.AppEvent")]
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
		private var textsController:TextsController;
		private var timerController:TimerController;
		
		public function MainController()
		{
		}
		
		public function init ( parameters : Object ) : void {
			debug("MainController:init");
			mediaController = new MediaController();
			mediaController.setParameters(parameters);
			mediaController.addEventListener(AppEvent.CONNECT_INIT_COMPLETE, appNetConnectHandler);
			mediaController.initConnection();
			userController = new UserController();
			userController.setParameters(parameters);
			streamController = new StreamController();
			presentationController = new PresentationController();
			textsController = new TextsController();
			textsController.addEventListener(AppEvent.LOAD_TEXTS_COMPLETE, loadTextsHandler);
			textsController.loadXML();
			soController = new SharedObjectController();
			soController.addEventListener(SharedObjectEvent.SHARED_PRESENTATION_UPLOADED, onSharedObjectEvent);
			soController.addEventListener(SharedObjectEvent.LESSON_IS_FINISHED, onSharedObjectEvent);
			soController.addEventListener(SharedObjectEvent.TIME_IS_OUT, onSharedObjectEvent);
			if (User.getInstance().partnerRoleID == User.STUDENT)
			{
				timerController = new TimerController();
				timerController.addEventListener(AimyaTimerEvent.TIMER_EVENT, onAimyaTimerEvent);
			}
			//soController.initSO();
			//this.mainUI = mainUI;
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

		public function onAppEvent ( event : AppEvent ) : void {
			debug("MainController:onAppEvent");
			switch (event.type){
				case (AppEvent.STOP_SESSION):	//soController.setSOProperty('endLesson' + User.getInstance().userID, "true");
												//endLesson();	
												break;
				case (AppEvent.CHANGE_SCREEN_STATE):	soController.setSOProperty('screenMode' + User.getInstance().userID, (!Main.getInstance().fsMode).toString());	
														break;
			}
		}
		
		public function onMediaEvent ( event : MediaEvent ) : void {
			debug("MainController:onAppEvent");
			switch (event.type){
				case (MediaEvent.MY_CAM_PAUSE_TOGGLE ):	streamController.myVideoMute();	
														break;
				case (MediaEvent.MY_MIC_PAUSE_TOGGLE ):	streamController.myAudioMute();	
														break;
				case (MediaEvent.PARTNER_CAM_PAUSE_TOGGLE ):	streamController.partnerVideoMute();	
																break;
				case (MediaEvent.PARTNER_MIC_PAUSE_TOGGLE ):	streamController.partnerAudioMute();	
																break;
			}
		}
		
		private function endLesson(initiator_id:String) : void {
			soController.closeConnect();
			streamController.closeConnect();
			// the sharedobject can not send the end lesson message that fast. need to close netconnect somehow later...
			mediaController.closeConnect();
			presentationController.clearImages();
			Notes.getInstance().clear();
			Main.getInstance().lesson_finished_by = initiator_id;
			timerController.stop();
			Main.getInstance().lesson_finished = true;
			//if(Main.getInstance().fsMode)
				//soController.setSOProperty('screenMode' + User.getInstance().userID, (!Main.getInstance().fsMode).toString());
		}
		
		public function onTextChatEvent ( event : NotesEvent ) : void {
			debug("MainController:onTextChatEvent");
			soController.setSOProperty("chatMessageData", event.value);
		}
		
		public function onServiceEvent ( event : ServiceEvent ) : void {
			debug("MainController:onServiceEvent");
			switch (event.type) {
				case (ServiceEvent.GET_PRESENTATION_IMAGES_RESULT) : 	presentationController.setImages(event.value as ArrayCollection);
																		soController.setSOProperty("imagesUrls", event.value as ArrayCollection);
																		break;
				case (ServiceEvent.SESSION_IS_STARTED_RESULT) : 		Main.getInstance().session_started = true;
																		break;
				case (ServiceEvent.SESSION_IS_STOPPED_RESULT) : 		Main.getInstance().session_started = false;
																		soController.setSOProperty('endLesson' + User.getInstance().userID, "true");
																		break;
				case (ServiceEvent.GET_CURRENT_TIME_RESULT) :	 		Main.getInstance().remainingTime = event.value.data as int;
																		break;
				case (ServiceEvent.RESIZE_RESULT) :				 		break;
			}
		}
		
		public function onAimyaTimerEvent( event : AimyaTimerEvent ) : void {
			if (soController.soIsInit())
				var rT:Number = Number(soController.getSOProperty("remainingTime"));
				rT -= TimerController.INTERVAL_IN_SEC;
				if (rT <= 0){
					rT = 0;
					timerController.stop();
				}
				soController.setSOProperty("remainingTime", String(rT)); 
		}
		
		public function onSharedObjectEvent ( event : SharedObjectEvent ) : void {
			debug("MainController:onSOEvent");
			dispatchEvent(event);
			switch (event.type) {
				/*case (SOEvent.SHARED_PRESENTATION_UPLOADED) : 	presentationController.setImages(event.value as ArrayCollection);
																break;*/
				case (SharedObjectEvent.LESSON_IS_FINISHED) : 	endLesson(event.value);
					break;
				case (SharedObjectEvent.TIME_IS_OUT)		 : 	// We don't need to end the lesson if time is out - teacher should do it himself
																//endLesson("0");
																//this.dispatchEvent( new AppEvent ( AppEvent.TIME_IS_OUT ) );
					break;
			}
		}
		
		private function appNetConnectHandler ( event : AppEvent ) : void {
			debug("MainController:appNetConnectHandler");
			//streamController.addEventListener(AppEvent.MY_STREAM_INIT_COMPLETE, onMyStreamInitComplete);
			streamController.initMyNetStream();
			streamController.initPartnerNetStream();
			soController.initSO();
			timerController.start();
			Media.getInstance().connected = true;
			initCompleteCheck();
		}
		
/*		public function initSO():void {
			soController = new SharedObjectController();
			soController.addEventListener(SharedObjectEvent.SHARED_PRESENTATION_UPLOADED, onSharedObjectEvent);
			soController.addEventListener(SharedObjectEvent.LESSON_IS_FINISHED, onSharedObjectEvent);
			soController.initSO();
		}*/
		
		private function loadTextsHandler ( event : AppEvent ) : void {
			Main.getInstance().texts_loaded = true;
			initCompleteCheck();
		}
		
		private function initCompleteCheck () : void {
			if (Main.getInstance().texts_loaded == true && Media.getInstance().connected == true)
			{
				this.dispatchEvent( new AppEvent ( AppEvent.INIT_COMPLETE ) );
			}
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}