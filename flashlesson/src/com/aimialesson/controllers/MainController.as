package com.aimialesson.controllers
{
	import com.aimialesson.events.*;
	import com.aimialesson.model.*;
	
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
			setParams(parameters);
			mediaController = new MediaController();
		//	mediaController.setParameters(parameters);
			mediaController.addEventListener(AppEvent.CONNECT_INIT_COMPLETE, appNetConnectHandler);
			mediaController.initConnection();
			userController = new UserController();
			//userController.setParameters(parameters);
			streamController = new StreamController();
			presentationController = new PresentationController();
			textsController = new TextsController();
			textsController.addEventListener(AppEvent.LOAD_TEXTS_COMPLETE, loadTextsHandler);
			textsController.loadXML();
			soController = new SharedObjectController();
			soController.addEventListener(SharedObjectEvent.SHARED_PRESENTATION_UPLOADED, onSharedObjectEvent);
			soController.addEventListener(SharedObjectEvent.LESSON_IS_FINISHED, onSharedObjectEvent);
			soController.addEventListener(SharedObjectEvent.TIME_IS_OUT, onSharedObjectEvent);
			
			//soController.initSO();
			//this.mainUI = mainUI;
			/*recorderController = new RecorderController();
			recorderController.init(mainUI);
			recorderController.startTransferring();*/
		}
		
		private function setParams ( parameters : Object ) : void {
			for (var i in parameters){
				debug (i+":"+parameters[i]);
			}
			if (parameters.myStreamName){
				Media.getInstance().myStreamName = parameters.myStreamName;
				debug (parameters.myStreamName);
			}
			if (parameters.partnerStreamName){
				Media.getInstance().partnerStreamName = parameters.partnerStreamName;
				debug (parameters.partnerStreamName);
			}
			if (parameters.soID){
				Media.getInstance().soID = parameters.soID;
				debug (parameters.soID);
			}
			if (parameters.domain){
				Actions.getInstance().domain = parameters.domain;
				debug (parameters.domain);
			}
			if (parameters.lang){
				Texts.getInstance().lang = String(parameters.lang).substring(1);
				Actions.getInstance().domain_add = parameters.lang;
				debug (parameters.lang);
			}
			if (parameters.focus_name){
				Main.getInstance().topic = parameters.focus_name;
				debug (parameters.topic);
			}
			if (parameters.booking_id){
				Main.getInstance().booking_id = parameters.booking_id;
				debug (parameters.booking_id);
			}
			if (parameters.total_time){
				Time.getInstance().totalTime = parameters.total_time;
				//		Main.getInstance().remainingTime = parameters.total_time;
				debug (parameters.total_time);
			}
			if (parameters.fs_mode){
				Main.getInstance().fsMode = (parameters.fs_mode == "1");
				debug (parameters.fs_mode);
			}
			if (parameters.userName){
				User.getInstance().userName = parameters.userName;
				debug ("userName:" + parameters.userName);
			}
			if (parameters.userRole){
				debug ("userRole:" + parameters.userRole);
				User.getInstance().userRoleID = parameters.userRole;
			}
			if (parameters.partnerName){
				User.getInstance().partnerName = parameters.partnerName;
				debug ("partnerName:" + parameters.partnerName);
			}
			if (parameters.PHPSESSID){
				User.getInstance().sessionID = parameters.PHPSESSID;
				debug ("sessionID(PHPSESSID):" + parameters.PHPSESSID);
			}
			if (parameters.lesson_id){
				User.getInstance().lesson_id = parameters.lesson_id;
				debug ("lessonID:" + parameters.lesson_id);
			}
			if (parameters.partnerId){
				User.getInstance().partnerID = parameters.partnerId;
				debug ("partnerId:" + parameters.partnerId);
			}
			if (parameters.userId){
				User.getInstance().userID = parameters.userId;
				debug ("userId:" + parameters.userId);
			}
			if (parameters.userTZ){
				User.getInstance().timeZone = Number((parameters.userTZ as String).substr(0,3));
				debug ("timeZone:" + parameters.userTZ);
			}
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
			presentationController.clearImages();
			Notes.getInstance().clear();
			Main.getInstance().lesson_finished_by = initiator_id;
			if (timerController) timerController.stop();
			Main.getInstance().lesson_finished = true;
			mediaController.closeConnect();
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
/*				case (ServiceEvent.GET_CURRENT_TIME_RESULT) :	 		Time.getInstance().remainingTime = event.value.data as int;
																		break;*/
				case (ServiceEvent.RESIZE_RESULT) :				 		break;
			}
		}
		
		public function onAimyaTimerEvent( event : AimyaTimerEvent ) : void {
			if (soController.soIsInit() && soController.getSOProperty("remainingTime")){
				var rT:Number = Number(soController.getSOProperty("remainingTime"));
				rT -= TimerController.INTERVAL_IN_SEC;
				if (rT <= 0){
					rT = 0;
					timerController.stop();
				}
				soController.setSOProperty("remainingTime", String(rT)); 
			}
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
			if (User.getInstance().partnerRoleID == User.STUDENT)
			{
				timerController = new TimerController();
				timerController.addEventListener(AimyaTimerEvent.TIMER_EVENT, onAimyaTimerEvent);
				timerController.start();
			}
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
			debug("MainController:loadTextsHandler");
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
			trace(str);
		}
	}
}