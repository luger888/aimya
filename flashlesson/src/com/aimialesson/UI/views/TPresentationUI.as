package com.aimialesson.UI.views
{
	//import com.aimialesson.UI.views.elements.PresentationBG;
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Presentation;
	
	import mx.graphics.BitmapScaleMode;
	
	import spark.components.supportClasses.SkinnableComponent;
	import spark.primitives.BitmapImage;
	
	[Event (name="moveToLeft", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="moveToRight", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="presentationUploaded", type="com.aimialesson.events.PresentationEvent")]
	public class TPresentationUI extends SkinnableComponent
	{
		[SkinPart (required="false")]
		public var presentation:PresentationUI;
		[SkinPart (required="false")]
		public var upload:UploadUI
		[SkinPart (required="false")]
		public var presentationBG:BitmapImage;
		//public var presentationBG:PresentationBG;
		[Bindable]
		public var prWidth:int;
		[Bindable]
		public var prHeight:int;
		
		private var presentationTrueWidth:int;
		private var presentationTrueHeight:int;
		
		public function TPresentationUI()
		{
			super();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			debug ( "TPresentationUI:" + partName );
			if ( instance == presentation ) {
				presentation.addEventListener( PresentationEvent.MOVE_TO_LEFT, onPresentationEvent );
				presentation.addEventListener( PresentationEvent.MOVE_TO_RIGHT, onPresentationEvent );
				presentation.invalidateDisplayList();
			} else if ( instance == upload ) {
				upload.addEventListener( PresentationEvent.PRESENTATION_UPLOADED, onPresentationEvent );
			} else if ( instance == presentationBG ) {
			}
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			
		}
		[Bindable]
		public var bgWidth:Number;
		[Bindable]
		public var bgHeight:Number;
		override protected function updateDisplayList ( unscaledWidth : Number, unscaledHeight : Number ) : void {
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			if (!presentationBG) return;
/*			if (unscaledHeight / unscaledWidth > presantationBG.sourceHeight / presantationBG.sourceWidth){
				presantationBG.percentWidth = 100;
				presantationBG.height = presantationBG.sourceHeight * ( unscaledWidth / presantationBG.sourceWidth );
			} else {
				presantationBG.percentHeight = 100;
				presantationBG.width = presantationBG.sourceWidth * ( unscaledHeight / presantationBG.sourceHeight );
			}*/
		}
		
		public function initSize () : void {
			if (presentationBG){
				presentationBG.percentHeight = 0;
				presentationBG.percentWidth = 0;
				presentationBG.height = 0;
				presentationBG.width = 0;
			}
			if (upload) upload.initSize();
		}
		
		private function onPresentationEvent ( event : PresentationEvent ) : void {
			debug("MainUI:onPresentationEvent " + event.type);
			this.dispatchEvent ( event );
		}
		
		private function debug ( mes : String) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += mes + "\n";
			trace(mes);
		}
	}
}