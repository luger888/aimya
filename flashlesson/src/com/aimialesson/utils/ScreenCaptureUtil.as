package com.aimialesson.utils
{
	import com.aimialesson.model.Main;
	
	import flash.display.BitmapData;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import flash.net.FileReference;
	import flash.utils.ByteArray;
	
	import mx.core.UIComponent;
	import mx.events.FlexEvent;
	import mx.graphics.codec.JPEGEncoder;
	
	public class ScreenCaptureUtil extends EventDispatcher
	{
		private const OUTPUT_WIDTH:Number = 800;
		private const OUTPUT_HEIGHT:Number = 550;
		private var source:UIComponent;
		public var byteArray:ByteArray;
		
		public function ScreenCaptureUtil( source : UIComponent ) : void
		{
			this.source = source;
		}
		
		public function record () : void {
			debug("ScreenCaptureUtil:record");
			//source.addEventListener(FlexEvent.CREATION_COMPLETE, onCreationComplete);
			//source.addEventListener(Event.ENTER_FRAME, storeBitmap);
			source.addEventListener(MouseEvent.CLICK, storeBitmap);
			byteArray = new ByteArray();
		}
		
		public function stop () : void {
			source.removeEventListener(Event.ENTER_FRAME, storeBitmap);
		}
		
		private function onCreationComplete ( e : FlexEvent ) : void {
			debug("ScreenCaptureUtil:onCreationComplete");
			source.addEventListener(Event.ENTER_FRAME, storeBitmap);
		}
		
		private function storeBitmap ( event : Event ) : void {
			debug("ScreenCaptureUtil:storeBitmap");
			var bmpd:BitmapData = new BitmapData(OUTPUT_WIDTH,OUTPUT_HEIGHT,false,0x0);
			bmpd.draw(source);
			var f:FileReference = new FileReference();
			var jpgenc:JPEGEncoder = new JPEGEncoder(80);
			var imgByteArray:ByteArray = jpgenc.encode(bmpd);
			f.save(imgByteArray, "img.jpg");
			byteArray.writeBytes(imgByteArray);
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}