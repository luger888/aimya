package com.aimialesson.controllers
{
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Presentation;
	
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class PresentationController extends EventDispatcher
	{
		public function PresentationController(target:IEventDispatcher=null)
		{
			super(target);
		}
		
		public function presentationEventHandler ( event : PresentationEvent ) : void {
			debug("PresentationController:presentationEventHandler " + event.type);
			switch (event.type) {
				case (PresentationEvent.MOVE_TO_LEFT) 	: 	goToPrevius();
															break;
				case (PresentationEvent.MOVE_TO_RIGHT) 	: 	goToNext();
															break;
			}
		}
		
		public function goToNext ( ) : void {
			Presentation.getInstance().currentImageNumber++;
		}
		
		public function goToPrevius ( ) : void {
			Presentation.getInstance().currentImageNumber--;
		}
		
		public function setImages ( images : ArrayCollection ) : void {
			Presentation.getInstance().imageUrls = images;
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
		
	}
}