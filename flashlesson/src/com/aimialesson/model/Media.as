package com.aimialesson.model
{
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.net.NetConnection;
	import flash.net.NetStream;

	public class Media
	{
		public static var CONNECTED_CHANGED:String = "connectedChange";
		
		//public var rtmp:String = "rtmp://localhost/oflaDemo";
		public var rtmp:String = "rtmp://184.169.133.140/oflaDemo";
		//public var rtmp:String = "rtmp://66.199.229.115/oflaDemo";
		//public var rtmp:String = "rtmp://localhost/videorecording";
		//public var rtmp:String = "rtmp://localhost/videochat";
		//public var rtmp:String = "rtmp://localhost/savelive1";
		//public var rtmp:String = "rtmp://demo6.flashmediaserver.it/livepkgr/livestream?adbe-live-event=liveevent";
		public var nc:NetConnection;
		public var myStreamName:String = "streamAlex";
		public var partnerStreamName:String = "streamAlex";
		public var myNetStream:NetStream;
		public var partnerNetStream:NetStream;
		public var soID:String = "12_55";
		public var audioSocketHost:String;
		public var videoSocketHost:String;
		public var audioSocketPort:int;
		public var videoSocketPort:int;
		public var cam:Camera;
		public var mic:Microphone;
		public var type:String = "live";
		[Bindable]
		public var camPaused:Boolean = false;
		[Bindable]
		public var micPaused:Boolean = false;
		[Bindable]
		public var partnerCamPaused:Boolean = false;
		[Bindable]
		public var partnerMicPaused:Boolean = false;
		
		private var _connected:Boolean = false;
		[Bindable(Event="connectedChange")]
		public function set connected ( value : Boolean ) : void {
			if (value != _connected){
				_connected = value;
				dispatchEvent ( new Event ( Media.CONNECTED_CHANGED ));
			}
		}
		public function get connected () : Boolean {
			return _connected;
		}
		
		private static var instance:Media;
		
		public function Media()
		{

		}
		
		public static function getInstance():Media {
			if (instance == null){
				instance = new Media();
			}
			return instance;
		}
		
	}
}