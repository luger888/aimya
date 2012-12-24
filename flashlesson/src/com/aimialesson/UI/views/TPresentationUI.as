package com.aimialesson.UI.views
{
	//import com.aimialesson.UI.views.elements.PresentationBG;
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Presentation;
	
	import mx.core.UIComponent;
	import mx.graphics.BitmapScaleMode;
	
	import spark.components.Group;
	import spark.components.supportClasses.SkinnableComponent;
	import spark.primitives.BitmapImage;
	import spark.primitives.Rect;
	
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
		public var group:Group;
		[SkinPart (required="false")]
		public var rectBG:Rect;
		
		//public var presentationBG:PresentationBG;
		[Bindable]
		public var prWidth:int;
		[Bindable]
		public var prHeight:int;
		
		public static const PRESENTATION_TRUE_WIDTH:int = 634;
		public static const PRESENTATION_TRUE_HEIGHT:int = 573;
		
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
			}
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			
		}
		
		[Bindable]
		public function set wScale ( value : Number ) : void {
			
		}
		
		public function get wScale () : Number {
			return PRESENTATION_TRUE_WIDTH / PRESENTATION_TRUE_HEIGHT;
		}
		
		
		
		[Bindable]
		public function set hScale ( value : Number ) : void {
			
		}
		
		public function get hScale () : Number {
			return PRESENTATION_TRUE_HEIGHT / PRESENTATION_TRUE_WIDTH;
		}
		
		[Bindable]
		public var bgWidth:Number;
		[Bindable]
		public var bgHeight:Number;
		override protected function updateDisplayList ( unscaledWidth : Number, unscaledHeight : Number ) : void {
			if (!(this.parent as UIComponent).percentWidth && !(this.parent as UIComponent).percentHeight){
				//this.width = PRESENTATION_TRUE_WIDTH;
				//this.height = PRESENTATION_TRUE_HEIGHT;
			}
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			if (unscaledHeight / unscaledWidth > PRESENTATION_TRUE_HEIGHT / PRESENTATION_TRUE_WIDTH){
				//group.percentWidth = 100;
				//group.height = group.width * hScale;
			} else {
				//group.percentHeight = 100;
				//group.width = group.height * wScale;
			}
		}
		
		public function initSize () : void {
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