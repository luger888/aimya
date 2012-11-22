package com.aimialesson.UI.views
{
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.User;
	
	import flash.display.LoaderInfo;
	import flash.events.IOErrorEvent;
	import flash.net.FileReference;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	
	import mx.controls.ProgressBar;
	import mx.formatters.NumberFormatter;
	
	import spark.components.Button;
	import spark.components.Label;
	import spark.components.supportClasses.SkinnableComponent;
	[Event (name="presentationUploaded", type="com.aimialesson.events.PresentationEvent")]
	public class UploadUI extends SkinnableComponent
	{
		import flash.events.Event;
		import flash.events.MouseEvent;
		import flash.events.ProgressEvent;
		
		[SkinPart (required="true")]
		public var uploadBtn:Button;
		[SkinPart (required="true")]
		public var message:Label;
		[SkinPart (required="true")]
		public var progressBar:ProgressBar;
		[SkinPart (required="true")]
		public var numberFormatter:NumberFormatter;
		private var fileRef:FileReference;
		
		public function UploadUI()
		{
			super();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{ 
			if (instance == uploadBtn){
				uploadBtn.addEventListener(MouseEvent.CLICK,browseAndUpload);
			} else {
				
			}
		}
		
		override protected function partRemoved ( partName : String, instance : Object ) : void {
			
		}
		
		override protected function attachSkin():void {
			super.attachSkin();
			init();
		}
		
		
		private function init():void {
			fileRef = new FileReference();
			fileRef.addEventListener(Event.SELECT, fileRef_select);
			fileRef.addEventListener(ProgressEvent.PROGRESS, fileRef_progress);
			fileRef.addEventListener(Event.COMPLETE, fileRef_complete);
			fileRef.addEventListener(IOErrorEvent.IO_ERROR, onIOErrorEvent);
		}
		
		private function browseAndUpload(event:MouseEvent):void {
			//just for test purpose
			debug ( "browseAndUpload" );
		//	this.dispatchEvent(new PresentationEvent ( PresentationEvent.PRESENTATION_UPLOADED ));
			fileRef.browse();
			message.text = "";
		}
		
		private function fileRef_select(evt:Event):void {
			try {
				//debug ("size (bytes): " + numberFormatter.format(fileRef.size));
				var urlRequest:URLRequest = new URLRequest(Actions.getInstance().fileUploadUrl);
				urlRequest.method = URLRequestMethod.POST;
				var variables : URLVariables = new URLVariables();  
				variables.PHPSESSID = User.getInstance().sessionID;  
				urlRequest.data = variables; 
				//	{PHPSESSID : User.getInstance().sessionID};
				debug ( "UploadUI fileRef_select PHPSESSID:" + User.getInstance().sessionID );
				debug ( "urlRequest.url:" + Actions.getInstance().fileUploadUrl );
				fileRef.upload(urlRequest);
			} catch (err:Error) {
				debug ( "ERROR: zero-byte file" );
			}
		}
		
		private function fileRef_progress(evt:ProgressEvent):void {
			progressBar.visible = true;
		}
		
		private function fileRef_complete(evt:Event):void {
			debug (" (complete)" );
			progressBar.visible = false;
			this.dispatchEvent(new PresentationEvent ( PresentationEvent.PRESENTATION_UPLOADED ));
		}
		
		private function onIOErrorEvent(event:IOErrorEvent):void {
			debug ("onIOErrorEvent");
		}

		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
			message.text = str;
		}
	}
}