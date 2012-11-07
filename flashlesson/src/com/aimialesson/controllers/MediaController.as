package com.aimialesson.controllers
{
	import com.aimialesson.events.AppEvent;
	import com.aimialesson.model.Client;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	
	import flash.events.EventDispatcher;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetConnection;
	
	import spark.components.Application;

	[Event (name="connectInitComplete", type="com.aimialesson.events.AppEvent")]
	public class MediaController extends EventDispatcher
	{
		public function MediaController()
		{
		}
		
		public function setParameters ( parameters : Object ) : void {
			if (parameters.myStreamName)
				Media.getInstance().myStreamName = parameters.myStreamName;
			if (parameters.partnerStreamName)
				Media.getInstance().partnerStreamName = parameters.partnerStreamName;
			if (parameters.soID)
				Media.getInstance().soID = parameters.soID;
			if (parameters.userName)
				Media.getInstance().userName = parameters.userName;

		}
		
		public function initConnection () : void {
			debug("MediaController:initConnection");
			Media.getInstance().nc = new NetConnection();
			Media.getInstance().nc.addEventListener(NetStatusEvent.NET_STATUS, netStatusHandler);
			Media.getInstance().nc.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			Media.getInstance().nc.client = new Client(); 
			Media.getInstance().nc.connect(Media.getInstance().rtmp);
		}
				
		private function netStatusHandler ( event : NetStatusEvent ) :void {
			debug("netStatusHandler");		
			debug(event.info.code);

			if (event.info.code == "NetConnection.Connect.Success") 
			{
				this.dispatchEvent( new AppEvent(AppEvent.CONNECT_INIT_COMPLETE));
			}
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