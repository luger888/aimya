package com.aimialesson.controllers
{
	import com.aimialesson.events.AppEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	
	import flash.events.*;
	import flash.net.NetStream;

//	[Event (name="myStreamInitComplete", type="com.aimialesson.events.AppEvent")]
//	[Event (name="partnerStreamInitComplete", type="com.aimialesson.events.AppEvent")]
	public class StreamController extends EventDispatcher
	{
		public function StreamController () : void {
		}
		
		public function initMyNetStream () : void {
			debug("StreamController:initMyNetStream");
			Media.getInstance().myNetStream = new NetStream(Media.getInstance().nc);
			Media.getInstance().myNetStream.client = this;
			Media.getInstance().myNetStream.addEventListener( NetStatusEvent.NET_STATUS, netStatusHandler );
			Media.getInstance().myNetStream.addEventListener( IOErrorEvent.IO_ERROR, netIOError );
			Media.getInstance().myNetStream.addEventListener( AsyncErrorEvent.ASYNC_ERROR, netASyncError );
		}
		
		public function initPartnerNetStream () : void {
			debug("StreamController:initPartnerNetStream");
			Media.getInstance().partnerNetStream = new NetStream(Media.getInstance().nc);
			Media.getInstance().partnerNetStream.client = this;
			Media.getInstance().partnerNetStream.addEventListener( NetStatusEvent.NET_STATUS, netStatusHandler );
			Media.getInstance().partnerNetStream.addEventListener( IOErrorEvent.IO_ERROR, netIOError );
			Media.getInstance().partnerNetStream.addEventListener( AsyncErrorEvent.ASYNC_ERROR, netASyncError );
		}
		
		protected function netStatusHandler( event : NetStatusEvent ) : void {
			debug("netStatusHandler");
			
			for ( var prop:String in event.info)
			{
				debug("prop : "+prop+" = "+event.info[prop]);
			}
			
			debug(event.info.code);
			
			if (event.info.code == "NetStream.Connect.Success"){
				//this.dispatchEvent( new AppEvent ( AppEvent.MY_STREAM_INIT_COMPLETE));
				//this.dispatchEvent( new AppEvent ( AppEvent.PARTNER_STREAM_INIT_COMPLETE));
			}
		}
		
		/*protected function netSecurityError( event : SecurityErrorEvent ) : void 
		{
		// Pass SecurityErrorEvent to Command.
		responder.fault( new SecurityErrorEvent ( SecurityErrorEvent.SECURITY_ERROR, false, true,
		"Security error - " + event.text ) );
		}*/
		
		protected function netIOError( event : IOErrorEvent ) : void {
			debug("Input/output error - " + event.text);
		}
		
		protected function netASyncError( event : AsyncErrorEvent ) : void {
			debug("Asynchronous code error - <i>" + event.error + "</i>");
		}
		
		private function debug(str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}