package com.aimialesson.UI.views
{
	import mx.core.Container;
	import mx.core.UIComponent;
	
	import spark.components.Group;
	
	
	public class MainUIContainerOld extends Group
	{
		
		private var _content:UIComponent;
		
		public function MainUIContainerOld()
		{
			super();
		}
		
		public function set content ( value : UIComponent ) : void {
			this.removeAllElements();
			_content = value;
			this.addElement(_content);
			_content.bottom = 0;
			_content.top = 0;
			_content.left = 0;
			_content.right = 0;
		}
		
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void{
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			if (_content){
				_content.bottom = 0;
				_content.top = 0;
				_content.left = 0;
				_content.right = 0;
			}
		} 
	}
}