package com.aimialesson.controllers
{
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.utils.MicRecorderUtil;
	import com.aimialesson.utils.ScreenCaptureUtil;
	
	import flash.display.BitmapData;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.media.Microphone;
	import flash.net.Socket;
	import flash.utils.Timer;
	
	import mx.core.UIComponent;
	import mx.graphics.codec.JPEGEncoder;

	public class RecorderController
	{
		private var bmpd:BitmapData;
		private const FLV_FRAMERATE:int = 30;
		private const SEND_DATA_INTERVAL:int = 1000;
		private var source:UIComponent;
		private var mic:Microphone;
		private var micUtil:MicRecorderUtil;
		private var screenUtil:ScreenCaptureUtil;
		private var audioSocket:Socket;
		private var videoSocket:Socket;
		private var updateDataTimer:Timer; 
		
		
		public function RecorderController()
		{
		}
		
		public function startTransferring () : void {
			debug("RecorderController:startTransferring");
			updateDataTimer.start();
			startUtils();
		}
		
		public function stopTransferring () : void {
			updateDataTimer.stop();
			stopUtils();
		}
		
		private function startUtils () : void {
			micUtil.record();
			screenUtil.record();
		}
		
		private function stopUtils () : void {
			micUtil.stop();
			screenUtil.stop();
		}
			
		public function init ( source : UIComponent ) : void {
			debug("RecorderController:init");
			micUtil = new MicRecorderUtil();
			screenUtil = new ScreenCaptureUtil(source);
			audioSocket = new Socket(Media.getInstance().audioSocketHost, Media.getInstance().audioSocketPort);
			videoSocket = new Socket(Media.getInstance().videoSocketHost, Media.getInstance().videoSocketPort);
			updateDataTimer = new Timer(SEND_DATA_INTERVAL);
			updateDataTimer.addEventListener(TimerEvent.TIMER, updateSockets);
		}
		
		private function updateSockets ( event : TimerEvent ) : void {
			debug("RecorderController:init");
			//return;
			//audioSocket.writeBytes(micUtil.byteArray);
			//videoSocket.writeBytes(screenUtil.byteArray);
			stopTransferring ();
			startTransferring();
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}