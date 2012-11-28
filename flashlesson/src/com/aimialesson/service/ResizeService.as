package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.User;
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	public class ResizeService extends AimiaService
	{
		
		public function ResizeService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().resizeUrl;
			params.full = Main.getInstance().fsMode?"1":"0";
		}
		
		public function addParams ( value : Object) : void {
		}
		
		override protected function onSuccess ( result : Object ) : void {
			this.dispatchEvent( new ServiceEvent ( ServiceEvent.RESIZE_RESULT));
		}		
	}
}