package com.aimialesson.controllers
{
	import com.aimialesson.events.SharedObjectEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.Notes;
	import com.aimialesson.model.Presentation;
	
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.SyncEvent;
	import flash.net.SharedObject;
	
	[Event (name="sharedPresentationUploaded", type="com.aimialesson.events.SharedObjectEvent")]
	public class SharedObjectController extends EventDispatcher
	{
		public function SharedObjectController(target:IEventDispatcher=null)
		{
			super(target);
		}
		
		private var so:SharedObject;
		
		public function initSO():void
		{
			so = SharedObject.getRemote(Media.getInstance().soID, Media.getInstance().nc.uri, false);
			//			so = SharedObject.getRemote("SampleChat", "rtmp://localhost/SOSample", false);
			so.addEventListener(SyncEvent.SYNC, soOnSync);
			so.client    = this;
			so.connect(Media.getInstance().nc);
		}

		public function setSOProperty(name:String, value:Object):void {
			so.setProperty(name, value);
		}
		
		private function soOnSync(event:SyncEvent):void
		{
			debug("soOnSync");
			
			for (var prop:String in so.data) 
			{
				debug("prop "+prop+" = "+so.data[prop]);
			}
			if (so.data['chatMessageData'] != null){
				Notes.getInstance().newLineData = so.data['chatMessageData'];
			}
			Presentation.getInstance().currentImageNumber = so.data['imageN'];
			if (so.data['uploaded'] == "true"){
				so.data['uploaded'] = "false;"
				dispatchEvent( new SharedObjectEvent ( SharedObjectEvent.SHARED_PRESENTATION_UPLOADED ) );
			} 
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}