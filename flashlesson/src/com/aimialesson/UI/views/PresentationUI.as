package com.aimialesson.UI.views
{
	import com.aimialesson.model.Main;
	import com.aimialesson.events.PresentationEvent;
	
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	
	import mx.controls.Image;
	
	import spark.components.Button;
	import spark.components.supportClasses.SkinnableComponent;
	
	[Event (name="MOVE_TO_LEFT", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="MOVE_TO_RIGHT", type="com.aimialesson.events.PresentationEvent")]
	public class PresentationUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var previusBtn:Button;
		[SkinPart (required="true")]
		public var nextBtn:Button;
		[SkinPart (required="true")]
		public var currentImage:Image;
		
		public function PresentationUI()
		{
			super();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if (instance == previusBtn || instance == nextBtn){
				(instance as EventDispatcher).addEventListener(MouseEvent.CLICK, onBtnClick);
			} else {
				
			}
		}
		
		override protected function partRemoved ( partName : String, instance : Object ) : void {
			
		}
		
		private function onBtnClick ( event : MouseEvent ) : void {
			debug("Presentation:MainUI:onBtnClick");
			switch (event.target){
				case previusBtn: 	this.dispatchEvent( new PresentationEvent ( PresentationEvent.MOVE_TO_LEFT ));
									break;
				case nextBtn: 		this.dispatchEvent( new PresentationEvent ( PresentationEvent.MOVE_TO_RIGHT ));
									break;
			}
		}
		
		/*public function setCurrentImage ( url : String ) : void {
			currentImage.source = url;
		}*/
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}