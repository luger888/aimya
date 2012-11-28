package com.aimialesson.UI.views
{
	import com.aimialesson.events.AppEvent;
	
	import flash.events.MouseEvent;
	
	import spark.components.Button;
	import spark.components.supportClasses.SkinnableComponent;
	
	public class HeaderUI extends SkinnableComponent
	{
		
		[SkinPart (required="true")]
		public var goFSBtn:Button;
		
		public function HeaderUI()
		{
			super();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if ( instance == goFSBtn ) {
				goFSBtn.addEventListener(MouseEvent.CLICK, onBtnClick);
			} 
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			
		}
		
		private function onBtnClick ( event : MouseEvent ) : void {
			if (event.target == goFSBtn) {
				dispatchEvent( new AppEvent ( AppEvent.CHANGE_SCREEN_STATE ) );
			}
		}
	}
}