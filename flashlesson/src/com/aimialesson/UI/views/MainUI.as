package com.aimialesson.UI.views
{
	import com.aimialesson.UI.skins.NotesSkin;
	import com.aimialesson.UI.views.elements.*;
	import com.aimialesson.events.AppEvent;
	import com.aimialesson.events.NotesEvent;
	import com.aimialesson.events.PresentationEvent;
	import com.aimialesson.model.Main;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	
	import mx.core.Container;
	import mx.core.FlexGlobals;
	import mx.core.UIComponent;
	
	import spark.components.Button;
	import spark.components.TextArea;
	import spark.components.supportClasses.SkinnableComponent;
	
	[Event (name="moveToLeft", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="moveToRight", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="presentationUploaded", type="com.aimialesson.events.PresentationEvent")]
	[Event (name="addNewLine", type="com.aimialesson.events.NotesEvent")]
	[Event (name="changeScreenState", type="com.aimialesson.events.AppEvent")]
	public class MainUI extends SkinnableComponent
	{
		[SkinPart (required="false")]
		public var videoChatContainer:MainUIContainer;
		[SkinPart (required="false")]
		public var notesContainer:MainUIContainer;
		[SkinPart (required="false")]
		public var tPresentationContainer:MainUIContainer;
/*		[SkinPart (required="false")]
		public var presentationContainer:MainUIContainer;
		[SkinPart (required="false")]
		public var uploadContainer:MainUIContainer;
		[SkinPart (required="true")]
		public var headerContainer:MainUIContainer;*/
		[SkinPart (required="false")]
		public var presentationTitleContainer:MainUIContainer;
		[SkinPart (required="false")]
		public var remainingTimeContainer:MainUIContainer;
		[SkinPart (required="false")]
		public var totalTimeContainer:MainUIContainer;
		[SkinPart (required="false")]
		public var onlineStudentContainer:MainUIContainer;
		[SkinPart (required="false")]
		public var goFSBtn:Button;
		[SkinPart (required="false")]
		public var startSessionBtn:Button;
		[SkinPart (required="false")]
		public var stopSessionBtn:Button;
		[SkinPart (required="false")]
		public var debugger:TextArea;
		
		[Bindable]
		public var stage_width:int;
		[Bindable]
		public var stage_height:int;
		
		public function MainUI()
		{
			super();
			this.addEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
		}
		
		override protected function partAdded ( partName : String, instance : Object) : void
		{
			if ( instance == debugger ) {
				Main.getInstance().debugger = debugger;
			} else if ( instance == tPresentationContainer ) {
				tPresentationContainer.content = tPresentationUI;
			} else if ( instance == notesContainer ) {
				notesContainer.content = notesUI;				
			//} else if ( instance == uploadContainer ) {
				//uploadContainer.content = uploadUI;
			//} else if ( instance == headerContainer ) {
				//headerContainer.content = headerUI;
			} else if ( instance == videoChatContainer ) {
				videoChatContainer.content = videoChatUI;
			} else if ( instance == presentationTitleContainer ) {
				presentationTitleContainer.content = presentationTitleUI;
			} else if ( instance == remainingTimeContainer ) {
				remainingTimeContainer.content = remainingTimeUI;
			} else if ( instance == onlineStudentContainer ) {
				onlineStudentContainer.content = onlineStudentUI;
			} else if ( instance == totalTimeContainer ) {
				totalTimeContainer.content = totalTimeUI;
			} else if ( instance == goFSBtn ) {
				goFSBtn.addEventListener(MouseEvent.CLICK, onBtnClick);
			} else if ( instance == startSessionBtn ) {
				startSessionBtn.addEventListener(MouseEvent.CLICK, onBtnClick);
			} else if ( instance == stopSessionBtn ) {
				stopSessionBtn.addEventListener(MouseEvent.CLICK, onBtnClick);
			} 
		}
		
		override protected function partRemoved ( partName : String, instance : Object) : void {
			trace("MainUI partRemoved:" + partName);
			if ( instance == tPresentationContainer ) {
				tPresentationUI.initSize();
			}
		}
		
		private function onAddedToStage(event:Event):void {
			if (stage){
				stage_width = FlexGlobals.topLevelApplication.width;
				stage_height = FlexGlobals.topLevelApplication.height;
			}
		}
		
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void{
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			if (stage){
				stage_width = FlexGlobals.topLevelApplication.width;
				stage_height = FlexGlobals.topLevelApplication.height;
			}
		}
		
		private var notes:NotesUI;
		public function get notesUI () : NotesUI {
			if (!notes) {
				notes = new NotesUI();
				notes.addEventListener( NotesEvent.ADD_NEW_LINE, onTextChatEvent );
			}
			return notes;
		}
		
		/*private var header:HeaderUI;
		public function get headerUI () : HeaderUI {
			if (!header) {
				header = new HeaderUI();
				header.addEventListener( AppEvent.CHANGE_SCREEN_STATE, onAppEvent );
			}
			return header;
		}*/
		
		/*private var upload:UploadUI;
		public function get uploadUI () : UploadUI {
			if (!upload) {
				upload = new UploadUI();
				upload.addEventListener( PresentationEvent.PRESENTATION_UPLOADED, onPresentationEvent );
			}
			return upload;
		}
		
		private var presentation:PresentationUI;
		public function get presentationUI () : PresentationUI {
			if (!presentation) {
				presentation = new PresentationUI();
				presentation.addEventListener( PresentationEvent.MOVE_TO_LEFT, onPresentationEvent );
				presentation.addEventListener( PresentationEvent.MOVE_TO_RIGHT, onPresentationEvent );
			}
			return presentation;
		}*/
		private var tPresentation:TPresentationUI;
		public function get tPresentationUI () : TPresentationUI {
			if (!tPresentation) {
				tPresentation = new TPresentationUI();
				tPresentation.addEventListener( PresentationEvent.MOVE_TO_LEFT, onPresentationEvent );
				tPresentation.addEventListener( PresentationEvent.MOVE_TO_RIGHT, onPresentationEvent );
				tPresentation.addEventListener( PresentationEvent.PRESENTATION_UPLOADED, onPresentationEvent );
			} else {
				tPresentation.initSize();
			}
			
			return tPresentation;
		}
		
		private var videoChat:VideoChatUI;
		public function get videoChatUI () : VideoChatUI {
			if (!videoChat) {
				videoChat = new VideoChatUI();
			}
			return videoChat;
		}
		
		private var presentationTitle:PresentationTitle;
		public function get presentationTitleUI () : PresentationTitle {
			if (!presentationTitle) {
				presentationTitle = new PresentationTitle();
			}
			return presentationTitle;
		}
		
		private var remainingTime:RemainingTime;
		public function get remainingTimeUI () : RemainingTime {
			if (!remainingTime) {
				remainingTime = new RemainingTime();
			}
			return remainingTime;
		}
		
		private var onlineStudent:OnlineStudent;
		public function get onlineStudentUI () : OnlineStudent {
			if (!onlineStudent) {
				onlineStudent = new OnlineStudent();
			}
			return onlineStudent;
		}
		
		private var totalTime:TotalTime;
		public function get totalTimeUI () : TotalTime {
			if (!totalTime) {
				totalTime = new TotalTime();
			}
			return totalTime;
		}
		
		
		public function connectionInit () : void {
			debug("connectionInit");
			videoChat.myVideoInit();
			videoChat.partnerVideoInit();
		}
		
		private function onBtnClick ( event : MouseEvent ) : void {
			switch (event.target) {
				case (goFSBtn):		dispatchEvent( new AppEvent ( AppEvent.CHANGE_SCREEN_STATE ) );
									break;
				case (startSessionBtn):	dispatchEvent( new AppEvent ( AppEvent.START_SESSION ) );
									break;
				case (stopSessionBtn):		dispatchEvent( new AppEvent ( AppEvent.STOP_SESSION ) );
									break;
			}			
		}
		
		private function onPresentationEvent ( event : PresentationEvent ) : void {
			debug("MainUI:onPresentationEvent " + event.type);
			this.dispatchEvent ( event );
		}
		
		private function onTextChatEvent ( event : NotesEvent ) : void {
			debug("MainUI:onTextChatEvent " + event.type);
			this.dispatchEvent ( event );
		}
		
		private function onAppEvent ( event : AppEvent ) : void {
			debug("MainUI:onAppEvent " + event.type);
			this.dispatchEvent ( event );
		}
		
		private function debug ( mes : String) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += mes + "\n";
		}
	}
}