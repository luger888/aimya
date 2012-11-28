package com.aimialesson.service
{

	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.Media;
	import com.aimialesson.model.User;
	import flash.events.IEventDispatcher;
	
	import mx.collections.ArrayCollection;
	
	[Event (name="getPresentationImagesResult", type="com.aimialesson.events.ServiceEvent")]
	public class PresentationService extends AimiaService
	{
		
		public function PresentationService(target:IEventDispatcher=null)
		{
			super(target);
			callUrl = Actions.getInstance().getPresentaionImagesUrl;
			params.lesson_id = User.getInstance().lesson_id;
		}
		
		override protected function onSuccess ( result : Object ) : void {
			var imagesAC:ArrayCollection = new ArrayCollection(result.data as Array); 
			this.dispatchEvent( new ServiceEvent ( ServiceEvent.GET_PRESENTATION_IMAGES_RESULT, imagesAC));
		}		
	}
}