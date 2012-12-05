package com.aimialesson.controllers
{
	import com.aimialesson.events.SharedObjectEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.Notes;
	import com.aimialesson.model.Presentation;
	import com.aimialesson.model.User;
	
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.SyncEvent;
	import flash.events.TimerEvent;
	import flash.net.SharedObject;
	import flash.utils.Timer;
	
	[Event (name="sharedPresentationUploaded", type="com.aimialesson.events.SharedObjectEvent")]
	public class SharedObjectController extends EventDispatcher
	{
		public function SharedObjectController(target:IEventDispatcher=null)
		{
			super(target);
		}
		
		private var so:SharedObject;
		private const USER_ONLINE_DELAY:int = 6000;
		private const PARTNER_ONLINE_DELAY:int = 12000;
		private var partnerIsOnlineTimer:Timer = new Timer(PARTNER_ONLINE_DELAY);
		private var userIsOnlineTimer:Timer = new Timer(USER_ONLINE_DELAY);
		
		public function initSO():void
		{
			so = SharedObject.getRemote(Media.getInstance().soID, Media.getInstance().nc.uri, false);
			//			so = SharedObject.getRemote("SampleChat", "rtmp://localhost/SOSample", false);
			so.addEventListener(SyncEvent.SYNC, soOnSync);
			so.client    = this;
			so.connect(Media.getInstance().nc);
			partnerIsOnlineTimer.addEventListener(TimerEvent.TIMER, checkPartnerOnline);
			userIsOnlineTimer.addEventListener(TimerEvent.TIMER, setOnlineUpdate);
			userIsOnlineTimer.start();
		}
		
		public function closeConnect():void {
//			
			setSOProperty('endLesson', "true");
			so.clear();
			so.close();
			userIsOnlineTimer.stop();
			partnerIsOnlineTimer.stop();
		}

		public function setSOProperty(name:String, value:Object):void {
			so.setProperty(name, value);
		}
		
		private function soOnSync(event:SyncEvent):void
		{
			debug("soOnSync");
			
			for (var prop:String in so.data) 
			{
				debug("prop "+prop+" = "+so.data[prop]);
			}
			if (so.data['chatMessageData'] != null){
				Notes.getInstance().newLineData = so.data['chatMessageData'];
			}
			Presentation.getInstance().currentImageNumber = so.data['imageN'];
			if (so.data['uploaded'] == "true"){
				so.data['uploaded'] = "false";
				dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.SHARED_PRESENTATION_UPLOADED ) );
			}
			if (so.data['User' + User.getInstance().partnerID + 'isOnline'] == "true"){
				setSOProperty('User' + User.getInstance().partnerID + 'isOnline', "false");
				User.getInstance().partnerIsOnline = true;
				partnerIsOnlineTimer.reset();
				partnerIsOnlineTimer.start();
			}
			if (so.data['endLesson'] == "true"){
				dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.LESSON_IS_FINISHED ) );
			}
		}
		
		private function checkPartnerOnline ( event : TimerEvent ) : void {
			User.getInstance().partnerIsOnline = false;
			debug("checkPartnerOnline");
		}
		
		private function setOnlineUpdate ( event : TimerEvent ) : void {
			setSOProperty('User' + User.getInstance().userID + 'isOnline', "true");
			debug("setOnlineUpdate");
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}