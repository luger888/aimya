package com.aimialesson.service
{
	import com.aimialesson.events.NotesEvent;
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.events.ServiceEvent;
	import com.aimialesson.events.SharedObjectEvent;
	import com.aimialesson.model.Main;
	
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	
	[Event (name="getPresentationImagesResult", type="com.aimialesson.events.ServiceEvent")]
	public class ServiceMap extends EventDispatcher
	{
		private var presentationService:PresentationService;
		private var userIsOnlineService:UserIsOnlineService;
		private var partnerIsOnlineService:PartnerIsOnlineService;
		private var addNoteService:AddNoteService;
		
		public function ServiceMap(target:IEventDispatcher=null)
		{
			super(target);
		}
		
		public function init () : void {
			presentationService = new PresentationService();
			presentationService.addEventListener(ServiceEvent.GET_PRESENTATION_IMAGES_RESULT, onServiceEvent);
			userIsOnlineService = new UserIsOnlineService();
			partnerIsOnlineService = new PartnerIsOnlineService();
			addNoteService = new AddNoteService();
			userIsOnlineService.makeCall();
			partnerIsOnlineService.makeCall();
		}
		
		public function onPresentationEvent ( event : PresentationEvent ) : void {
			debug ( "ServiceMap : onPresentationEvent : " + event.type );
			if (event.type == PresentationEvent.PRESENTATION_UPLOADED){
				presentationService.makeCall();
			}
		}
		
		public function onNotesEvent ( event : NotesEvent ) : void {
			debug ( "ServiceMap : onPresentationEvent : " + event.type );
			if (event.type == NotesEvent.ADD_NEW_LINE){
				addNoteService.addParams(event.value);
				addNoteService.makeCall();
			}
		}
		
		public function onSharedObjectEvent ( event : SharedObjectEvent ) : void {
			debug ( "ServiceMap : onSOEvent : " + event.type );
			switch (event.type) {
				case (SharedObjectEvent.SHARED_PRESENTATION_UPLOADED) : 	presentationService.makeCall();
																break;
			}
		}
		
		private function onServiceEvent ( event : ServiceEvent ) : void {
			debug ( "ServiceMap : onServiceEvent : " + event.type );
			this.dispatchEvent(event);
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}