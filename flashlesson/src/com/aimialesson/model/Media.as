package com.aimialesson.model
{
	import flash.net.NetConnection;
	import flash.net.NetStream;

	public class Media
	{
		//public var rtmp:String = "rtmp://localhost/SOSample";
		public var rtmp:String = "rtmp://localhost/oflaDemo";
		public var nc:NetConnection;
		public var myStreamName:String = "MyStream";
		public var partnerStreamName:String = "MyStream";
		public var myNetStream:NetStream;
		public var partnerNetStream:NetStream;
		public var chatID:String = "SampleChat";

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