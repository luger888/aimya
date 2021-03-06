package com.aimialesson.controllers
{
	import com.aimialesson.events.SharedObjectEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.Notes;
	import com.aimialesson.model.Presentation;
	import com.aimialesson.model.User;
	import com.aimialesson.model.Time;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.NetStatusEvent;
	import flash.events.SyncEvent;
	import flash.events.TimerEvent;
	import flash.net.SharedObject;
	import flash.utils.Timer;
	
	import mx.collections.ArrayCollection;
	import mx.core.FlexGlobals;
	
	[Event (name="sharedPresentationUploaded", type="com.aimialesson.events.SharedObjectEvent")]
	[Event (name="lessonIsFinished", type="com.aimialesson.events.SharedObjectEvent")]
	public class SharedObjectController extends EventDispatcher
	{
		public function SharedObjectController(target:IEventDispatcher=null)
		{
			super(target);
		}
		
		private var so:SharedObject;
		private const USER_ONLINE_DELAY:int = 10000;
		private const PARTNER_ONLINE_DELAY:int = 20000;
		private var partnerIsOnlineTimer:Timer = new Timer(PARTNER_ONLINE_DELAY);
		private var userIsOnlineTimer:Timer = new Timer(USER_ONLINE_DELAY);
		
		public function initSO():void
		{
			debug("initSO:");	
			so = SharedObject.getRemote(Media.getInstance().soID, Media.getInstance().nc.uri, true);
			so.addEventListener(SyncEvent.SYNC, soOnSync);
			so.client = this;
			so.connect(Media.getInstance().nc);
			partnerIsOnlineTimer.addEventListener(TimerEvent.TIMER, checkPartnerOnline);
			userIsOnlineTimer.addEventListener(TimerEvent.TIMER, setOnlineUpdate);
			userIsOnlineTimer.start();
		}
		
		public function initStates () : void {
			debug("initModels:");
			for (var prop:String in so.data) 
			{
				debug("prop "+prop+" = "+so.data[prop]);
			}
			
			if (so.data["imagesUrls"]){
				Presentation.getInstance().imageUrls = so.data["imagesUrls"]; 
			} else {
			//	setSOProperty('imagesUrls', new ArrayCollection);
			}
			if (so.data["notes"]){
				Notes.getInstance().notesAC = new ArrayCollection(so.data["notes"] as Array);
				debug (so.data["notes"].length);
			}
			Presentation.getInstance().currentImageNumber = so.data['imageN'];
			/*if (so.data['uploaded'] == "true"){
				so.data['uploaded'] = "false";
				dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.SHARED_PRESENTATION_UPLOADED ) );
			}*/
			if (so.data['User' + User.getInstance().partnerID + 'isOnline'] == "true"){
				setSOProperty('User' + User.getInstance().partnerID + 'isOnline', "false");
				User.getInstance().partnerIsOnline = true;
				partnerIsOnlineTimer.reset();
				partnerIsOnlineTimer.start();
			}
			if ( so.data['remainingTime']){
				//setSOProperty("remainingTime", Main.getInstance().totalTime);
				Time.getInstance().remainingTime = Number(so.data['remainingTime']);
				if (Time.getInstance().remainingTime <= 0)
					this.dispatchEvent( new SharedObjectEvent (SharedObjectEvent.TIME_IS_OUT) );
			} else {
				setSOProperty("remainingTime", Time.getInstance().totalTime);
			}
			if (so.data['endLesson' + User.getInstance().partnerID] == "true"){
				dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.LESSON_IS_FINISHED, User.getInstance().partnerName  ) );
			}
			if (so.data['endLesson' + User.getInstance().userID] == "true"){
				dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.LESSON_IS_FINISHED, User.getInstance().userName  ) );
			}
			if (so.data['videoMute' + User.getInstance().partnerID] ){
				Media.getInstance().partnerCamPaused =  (so.data['videoMute' + User.getInstance().partnerID] == "true") ? true : false;
			}
			/*if (so.data['screenMode' + User.getInstance().userID] == "true"){
				// hack for usual case (not for refreshing on maximize mode in webkit based browsers etc...)
				if (FlexGlobals.topLevelApplication.width != Main.NORMAL_WIDTH)
					Main.getInstance().fsMode = true;
			} else {
				Main.getInstance().fsMode = false;
			}*/
		}
		
		public function closeConnect():void {
//			
//			setSOProperty('endLesson', "true");
			userIsOnlineTimer.stop();
			partnerIsOnlineTimer.stop();
			User.getInstance().partnerIsOnline = false;
			User.getInstance().isOnline = false;
			so.clear();
			so.close();
		}

		public function setSOProperty(name:String, value:Object):void {
			if (!Main.getInstance().isServer)so.setProperty(name, value); // to escape all calls from app from server
		}
		
		public function getSOProperty(name:String) : String {
			return so.data[name];
		}
		
		public function soIsInit():Boolean{
			return (so != null && so.data != null);
		}
		
		private var initialized:Boolean = false;
		private function soOnSync(event:SyncEvent):void
		{
			//debug("soOnSync");
			if (!initialized){
				initStates();
				initialized = true;
				return;
			}
			var changedList:Array = event.changeList;
			
			for (var i:int = 0; i < changedList.length; i++){
				//debug(changedList[i].name);
				switch (changedList[i].name){
					case "chatMessageData"	: 	if (so.data['chatMessageData'] != null){
													Notes.getInstance().newLineData = so.data['chatMessageData'];
													setSOProperty('notes', "");
													setSOProperty('notes', Notes.getInstance().notesAC.source);
												}
												break;
					case "notes"			:	//Notes.getInstance().notesAC = new ArrayCollection(so.data['notes'] as Array);
												break;
					case "imagesUrls"		:	//Presentation.getInstance().imageUrls = so.data['imagesUrls'];
												break;
					case "uploaded"			:	if (so.data['uploaded'] == "true"){
													setSOProperty('uploaded', "false");
													dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.SHARED_PRESENTATION_UPLOADED ) );
												}
												break;
					case "imageN"			:	Presentation.getInstance().currentImageNumber = so.data['imageN'];
												break;
					case 'User' + User.getInstance().partnerID + 'isOnline' : if (so.data[changedList[i].name] == "true"){
																					debug("partnerIsOnline");
																					setSOProperty('User' + User.getInstance().partnerID + 'isOnline', "false");
																					User.getInstance().partnerIsOnline = true;
																					partnerIsOnlineTimer.reset();
																					partnerIsOnlineTimer.start();
																				}
																				break;
					case 'videoMute' + User.getInstance().partnerID : 	debug(changedList[i].name);
																		Media.getInstance().partnerCamPaused =  (so.data[changedList[i].name] == "true") ? true : false;
																		break;
					case 'endLesson'  + User.getInstance().partnerID		:	if (so.data[changedList[i].name] == "true"){
																					dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.LESSON_IS_FINISHED, User.getInstance().partnerName ) );
																				}
																				break;
					case 'endLesson'  + User.getInstance().userID		:	if (so.data[changedList[i].name] == "true"){
																					dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.LESSON_IS_FINISHED, User.getInstance().userName ) );
																				}
																				break;
					case 'remainingTime'								:	Time.getInstance().remainingTime = Number(so.data[changedList[i].name]); 
																			if (Time.getInstance().remainingTime <= 0)
																				this.dispatchEvent( new SharedObjectEvent (SharedObjectEvent.TIME_IS_OUT) );
																			break;
					/*case 'screenMode' + User.getInstance().userID	:	if (so.data[changedList[i].name] == "true"){
																			Main.getInstance().fsMode = true;   
																		} else {
																			Main.getInstance().fsMode = false;
																		} 
																		Main.getInstance().dispatchEvent ( new Event ( Main.SCREEN_MODE_CHANGED ));
																		break;*/
						
				}
			}
		}
		
		private function checkPartnerOnline ( event : TimerEvent ) : void {
			User.getInstance().partnerIsOnline = false;
			debug("checkPartnerOnline");
		}
		
		private function setOnlineUpdate ( event : TimerEvent ) : void {
			setSOProperty('User' + User.getInstance().userID + 'isOnline', "true");
		//	debug("setOnlineUpdate");
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}