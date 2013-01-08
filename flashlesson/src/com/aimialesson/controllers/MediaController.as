package com.aimialesson.controllers
{
	import com.aimialesson.events.AppEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.Client;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.Texts;
	import com.aimialesson.model.Time;
	
	import flash.events.EventDispatcher;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.events.TimerEvent;
	import flash.net.NetConnection;
	import flash.utils.Timer;
	
	import spark.components.Application;

	[Event (name="connectInitComplete", type="com.aimialesson.events.AppEvent")]
	public class MediaController extends EventDispatcher
	{
		private var timer:Timer = new Timer (1000);
	
		public function MediaController()
		{
			timer.addEventListener(TimerEvent.TIMER, reconnect);
		}
		
		/*public function setParameters ( parameters : Object ) : void {
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
		}*/
		
		public function initConnection () : void {
			debug("MediaController:initConnection");
			Media.getInstance().nc = new NetConnection();
			Media.getInstance().nc.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
			Media.getInstance().nc.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			Media.getInstance().nc.client = new Client(); 
			Media.getInstance().nc.connect(Media.getInstance().rtmp);
		}
		
		public function closeConnect () : void {
			debug("MediaController:closeConnection");
			Media.getInstance().nc.close();
		}
				
		private function netStatusHandler ( event : NetStatusEvent ) :void {
			debug("netStatusHandler");		
			debug(event.info.code);

			if (event.info.code == "NetConnection.Connect.Success") 
			{
				debug ("nc.connected:" + Media.getInstance().nc.connected.toString());
				if (Media.getInstance().nc.connected){
					this.dispatchEvent( new AppEvent(AppEvent.CONNECT_INIT_COMPLETE));
				}
				timer.stop();
			}
			if (event.info.code == "NetConnection.Connect.Closed") 
			{
//				Media.getInstance().nc.removeEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
//				Media.getInstance().nc.removeEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
				Media.getInstance().connected = false;
				if (!Main.getInstance().lesson_finished){
					Media.getInstance().nc = new NetConnection();
					Media.getInstance().nc.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
					Media.getInstance().nc.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
					Media.getInstance().nc.client = new Client(); 
					if (!timer.running) timer.start();
				}
			}
		}
		
		private function reconnect(event:TimerEvent) : void {
			Media.getInstance().nc.connect(Media.getInstance().rtmp );
		}
		
		private function securityErrorHandler ( event : SecurityErrorEvent ) : void {
			debug("securityErrorHandler");
			
			for (var prop:String in event) 
			{
				debug("prop "+prop+" = "+event[prop]);
			}
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}