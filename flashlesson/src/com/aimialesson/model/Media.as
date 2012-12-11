package com.aimialesson.model
{
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.net.NetConnection;
	import flash.net.NetStream;

	public class Media
	{
		//public var rtmp:String = "rtmp://localhost/oflaDemo";
		public var rtmp:String = "rtmp://66.199.229.115/oflaDemo";
		//public var rtmp:String = "rtmp://localhost/videorecording";
		//public var rtmp:String = "rtmp://localhost/videochat";
		//public var rtmp:String = "rtmp://localhost/savelive1";
		public var nc:NetConnection;
		public var myStreamName:String = "streamAlex";
		public var partnerStreamName:String = "streamAlex";
		public var myNetStream:NetStream;
		public var partnerNetStream:NetStream;
		public var soID:String = "SampleSO";
		public var audioSocketHost:String;
		public var videoSocketHost:String;
		public var audioSocketPort:int;
		public var videoSocketPort:int;
		public var cam:Camera;
		public var mic:Microphone;
		[Bindable]
		public var camPaused:Boolean = false;
		[Bindable]
		public var micPaused:Boolean = false;
		[Bindable]
		public var partnerCamPaused:Boolean = false;
		[Bindable]
		public var partnerMicPaused:Boolean = false;
				
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