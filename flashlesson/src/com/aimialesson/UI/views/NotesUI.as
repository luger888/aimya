package com.aimialesson.UI.views
{
	import com.aimialesson.UI.skins.NotesSkin;
	import com.aimialesson.events.NotesEvent;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Notes;
	import com.aimialesson.model.User;
	
	import caurina.transitions.Tweener;
	import caurina.transitions.properties.CurveModifiers;
	
	import flash.events.*;
	
	import mx.collections.ArrayCollection;
	import mx.collections.ListCollectionView;
	import mx.events.CollectionEvent;
	import mx.events.FlexEvent;
	
	import spark.components.Button;
	import spark.components.List;
	import spark.components.TextArea;
	import spark.components.TextInput;
	import spark.components.supportClasses.SkinnableComponent;

	[Event (name="addNewLine", type="com.aimialesson.events.NotesEvent")]
	public class NotesUI extends SkinnableComponent
	{
		[SkinPart (required="true")]
		public var notesList:List;
		[SkinPart (required="true")]
		public var messageInput:TextArea;
		[SkinPart (required="true")]
		public var sendBtn:Button;
		
		private var motionEasing:String = "linear";
		private var motionTime:Number = 0.5;
		
		public function NotesUI()
		{
			super();
			//setStyle("skinClass", NotesSkin);
			Notes.getInstance().addEventListener(Notes.NEW_LINE_ADDED, onNewLine );	
		}
				
		override protected function partAdded(partName:String, instance:Object):void
		{
			trace("NotesUI:partAdded:" + instance);
			if (instance == notesList) {
	//			user = User.getInstance();
				//initChat();
				//notesList.dataProvider = Notes.getInstance().notesAC;
				skin.currentState = "unchangedState";
			} else {
				if (instance == sendBtn){
					sendBtn.addEventListener(MouseEvent.CLICK, onClickSendBtn);
				}
				if (instance == messageInput) {
					messageInput.addEventListener(KeyboardEvent.KEY_DOWN, onKeyDown);
				}
			}
		}
		override protected function partRemoved(partName:String, instance:Object):void {
			
		}
		
		private function onNewLine( event : Event ) : void {
			trace("NotesUI:onNewLine");
//			if (notesList.dataProvider as ArrayCollection) 
	//			(notesList.dataProvider as ArrayCollection).refresh();
			skin.currentState = "changedState";
			notesList.layout.verticalScrollPosition=notesList.dataGroup.contentHeight - notesList.height;
			callLater(setVerticalPosition);
		}
		
		private function setVerticalPosition(): void {
			Tweener.addTween(notesList.layout, {verticalScrollPosition:notesList.dataGroup.contentHeight - notesList.height, time:motionTime,  transition:this.motionEasing});
//			notesList.layout.verticalScrollPosition = notesList.dataGroup.contentHeight - notesList.height;
		}
		
		private function onClickSendBtn ( event : MouseEvent ) : void
		{
	//		messageInput.text = Media.getInstance().userName + ": " + messageInput.text;
			var date:Date = new Date();
			debug ("date.getTime():" + date.getTime());
			date.hours += User.getInstance().timeZone; 
			this.dispatchEvent( new NotesEvent ( NotesEvent.ADD_NEW_LINE,  {message:messageInput.text, name:User.getInstance().userName, date:date.getTime()}) );
			messageInput.text = "";
		}
		
		private function onKeyDown(ev:KeyboardEvent):void {
			if (ev.keyCode==13){
				onClickSendBtn(null);
			}
		}
		
		private function debug (str:String):void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}
