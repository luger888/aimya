package com.aimialesson.UI.views
{
	import spark.components.supportClasses.SkinnableComponent;
	
	public class HeaderUI extends SkinnableComponent
	{
		public function HeaderUI()
		{
			super();
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			/*if ( instance == debugger ) {
			} */
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			
		}
	}
}