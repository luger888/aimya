package com.aimialesson.UI.views
{
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import flash.events.ProgressEvent;
	
	import mx.controls.Image;
	import mx.events.FlexEvent;
	
	import spark.components.Button;
	import spark.components.supportClasses.SkinnableComponent;
	
	[Event (name="moveToLeft", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="moveToRight", type="com.aimialesson.events.PresentationEvent")]
	public class PresentationUI extends SkinnableComponent
	{
		[SkinPart (required="false")]
		public var previusBtn:Button;
		[SkinPart (required="false")]
		public var nextBtn:Button;
		[SkinPart (required="false")]
		public var currentImage:Image;
		
		public function PresentationUI()
		{
			super();
			this.addEventListener(FlexEvent.CREATION_COMPLETE, onCreationComplete);
		}
		
		
		private function onCreationComplete(event:FlexEvent) : void {
			this.removeEventListener(FlexEvent.CREATION_COMPLETE, onCreationComplete);
			this.invalidateDisplayList();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if (instance == previusBtn || instance == nextBtn){
				(instance as EventDispatcher).addEventListener(MouseEvent.CLICK, onBtnClick);
			} else if (instance == currentImage) {
			}
		}
		
		override protected function partRemoved ( partName : String, instance : Object ) : void {
			if (instance == previusBtn || instance == nextBtn){
				(instance as EventDispatcher).removeEventListener(MouseEvent.CLICK, onBtnClick);
			} else {
				
			}
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
		
		override protected function updateDisplayList ( unscaledWidth : Number, unscaledHeight : Number ) : void {
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			if (!currentImage || !currentImage.loaderInfo) return;
/*			if (this.height / this.width > currentImage.loaderInfo.height / currentImage.loaderInfo.width){
				currentImage.width = this.width - 20;
				currentImage.height = currentImage.loaderInfo.height * ( ( this.width - 20 ) / currentImage.loaderInfo.width );				 
			} else {
				currentImage.height = this.height - 20;
				currentImage.width = currentImage.loaderInfo.width * ( ( this.height - 20 ) / currentImage.loaderInfo.height );
			}*/
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}