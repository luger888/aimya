package com.aimialesson.controllers
{
	import com.aimialesson.events.AppEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	
	import flash.events.*;
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.media.MicrophoneEnhancedMode;
	import flash.media.MicrophoneEnhancedOptions;
	import flash.media.SoundCodec;
	import flash.media.SoundTransform;
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
			if (Main.getInstance().isServer){
				Media.getInstance().myNetStream.client = this;
				Media.getInstance().myNetStream.maxPauseBufferTime = 0.1;
				Media.getInstance().myNetStream.bufferTime = 0.02;
				Media.getInstance().myNetStream.play(Media.getInstance().myStreamName);
			} else {
				myVideoInit();
			}
		}
		
		public function initPartnerNetStream () : void {
			debug("StreamController:initPartnerNetStream");
			Media.getInstance().partnerNetStream = new NetStream(Media.getInstance().nc);
			Media.getInstance().partnerNetStream.client = this;
			Media.getInstance().partnerNetStream.maxPauseBufferTime = 0.1;
			Media.getInstance().partnerNetStream.bufferTime = 0.02;
			Media.getInstance().partnerNetStream.bufferTimeMax = 0.2;
			Media.getInstance().partnerNetStream.addEventListener( NetStatusEvent.NET_STATUS, netStatusHandler );
			Media.getInstance().partnerNetStream.addEventListener( IOErrorEvent.IO_ERROR, netIOError );
			Media.getInstance().partnerNetStream.addEventListener( AsyncErrorEvent.ASYNC_ERROR, netASyncError );
			Media.getInstance().partnerNetStream.play(Media.getInstance().partnerStreamName);
		}
		
		public function closeConnect() : void {
			debug("StreamController:closeConnect");
			Media.getInstance().myNetStream.close();
			Media.getInstance().partnerNetStream.close();
		}
				
		protected function netStatusHandler( event : NetStatusEvent ) : void {
		//	debug("netStatusHandler");
			
		/*	for ( var prop:String in event.info)
			{
				debug("prop : "+prop+" = "+event.info[prop]);
			}
			
			debug(event.info.code);*/
			
			if (event.info.code == "NetStream.Connect.Success"){
				//this.dispatchEvent( new AppEvent ( AppEvent.MY_STREAM_INIT_COMPLETE));
				//this.dispatchEvent( new AppEvent ( AppEvent.PARTNER_STREAM_INIT_COMPLETE));
			}
		}
		
		public function myVideoInit():void {
			debug("StreamController:myVideoInit");
			Media.getInstance().cam = Camera.getCamera();
			//Media.getInstance().mic = Microphone.getMicrophone(); 
			Media.getInstance().mic = Microphone.getEnhancedMicrophone();
			var options:MicrophoneEnhancedOptions = Media.getInstance().mic.enhancedOptions; 
			//options.mode = MicrophoneEnhancedMode.FULL_DUPLEX; 
			//options.echoPath = 256;
		//	options.autoGain = true;
			//options.nonLinearProcessing = true;
			if (Media.getInstance().mic == null){
				Media.getInstance().mic = Microphone.getMicrophone();
			}
			if ( Media.getInstance().cam != null ) 
			{
				debug("StreamController:myNetStream.attachCamera");
				//Media.getInstance().cam.setMode(320, 240, 15);
				Media.getInstance().cam.setMode(240, 180, 15);
				Media.getInstance().cam.setQuality(0, 0);
				Media.getInstance().cam.setKeyFrameInterval( 5 );

				Media.getInstance().myNetStream.attachCamera(Media.getInstance().cam);
			}
			if ( Media.getInstance().mic != null) 
			{
				var transform : SoundTransform = Media.getInstance().mic.soundTransform;
				transform.volume = 0;
				Media.getInstance().mic.soundTransform = transform;
				Media.getInstance().mic.setLoopBack(false);
//				Media.getInstance().mic.encodeQuality = 10;
				Media.getInstance().mic.setUseEchoSuppression(true);
				Media.getInstance().mic.gain = 50;
				Media.getInstance().mic.rate = 11;
				Media.getInstance().mic.codec = SoundCodec.SPEEX;
				Media.getInstance().mic.noiseSuppressionLevel = -30;
				Media.getInstance().mic.framesPerPacket = 1;
				Media.getInstance().mic.setSilenceLevel( 0, 2000);
				Media.getInstance().myNetStream.attachAudio(Media.getInstance().mic);
			}
			//Media.getInstance().myNetStream.bufferTime = 2;
			Media.getInstance().myNetStream.publish(Media.getInstance().myStreamName, "live");
		}
		


		/*private function onSessionStartedChange (event:Event) : void {
			if (Main.getInstance().session_started) Media.getInstance().partnerNetStream.play(Media.getInstance().partnerStreamName);
			else Media.getInstance().partnerNetStream.pause();
		}*/

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
		
		public function myVideoMute():void {
			Media.getInstance().camPaused = !Media.getInstance().camPaused;
			if (Media.getInstance().camPaused){
				Media.getInstance().myNetStream.attachCamera(null);
			} else {
				Media.getInstance().myNetStream.attachCamera(Media.getInstance().cam);
			}
		}
		
		public function myAudioMute():void {
			Media.getInstance().micPaused = !Media.getInstance().micPaused;
			if (Media.getInstance().micPaused){
				Media.getInstance().myNetStream.attachAudio(null);
			} else {
				Media.getInstance().myNetStream.attachAudio(Media.getInstance().mic);
			}
		}
		
		public function partnerVideoMute():void {
			Media.getInstance().partnerCamPaused = !Media.getInstance().partnerCamPaused;
			Media.getInstance().partnerNetStream.receiveVideo(!Media.getInstance().partnerCamPaused);
			//not sure why i need to do it, but other way receiveVideo works buggly
			//if (!Media.getInstance().partnerCamPaused)	Media.getInstance().partnerNetStream.resume();
		}
		
		public function partnerAudioMute():void {
			Media.getInstance().partnerMicPaused = !Media.getInstance().partnerMicPaused;
			debug ("partnerAudioMute:" + Media.getInstance().partnerMicPaused);
			Media.getInstance().partnerNetStream.receiveAudio(!Media.getInstance().partnerMicPaused);
			
			//not sure why i need to do it, but other way receiveAudio works buggly
			//if (!Media.getInstance().partnerMicPaused)	Media.getInstance().partnerNetStream.resume();
		}
		
		private function debug(str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}

	}
}