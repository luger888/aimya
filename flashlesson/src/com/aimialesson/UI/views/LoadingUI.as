package com.aimialesson.UI.views
{
	
	import com.aimialesson.UI.views.windows.LoadingWindow;
	
	import spark.components.supportClasses.SkinnableComponent;
	
	public class LoadingUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var loadingWindow:LoadingWindow;
		
		
		public function LoadingUI()
		{
			super();	
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if ( instance == loadingWindow ) {
				loadingWindow.text = "Connecting..."
			} 
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
		}
		
		public function connectFailed ( ) : void {
			if (loadingWindow)
				loadingWindow.text = "Can not connect to stream server";
		}
		
		public function loadTextsFailed ( ) : void {
			if (loadingWindow)
				loadingWindow.text = "Can not load lessontexts.xml";
		}
		
	}
}