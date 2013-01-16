package com.aimialesson.controllers
{
	import com.aimialesson.events.AppEvent;
	import com.aimialesson.model.Actions;
	import com.aimialesson.model.Main;
	import com.aimialesson.model.Texts;
	
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.xml.XMLDocument;
	import flash.xml.XMLNode;
	
	import mx.collections.ArrayCollection;
	import mx.collections.XMLListCollection;
	
	[Event (name="loadTextsComplete", type="com.aimialesson.events.AppEvent")]
	public class TextsController extends EventDispatcher
	{
		public const text_xml_url:String = "/flash/lessontexts.xml";
		//public const text_xml_url:String = "lessontexts.xml";
		private var loader:URLLoader = new URLLoader();
		public var textsXML:XML;
	
		public function TextsController(target:IEventDispatcher=null)
		{
			super(target);
			debug ("TextsController");
		}		

		public function loadXML():void {
			debug ("loadXML");
			loader.addEventListener(Event.COMPLETE, onLoaded);
			loader.addEventListener(IOErrorEvent.IO_ERROR, onIOError);
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError);
			var urlRequest:URLRequest = new URLRequest(Actions.getInstance().domain + text_xml_url);
			//var urlRequest:URLRequest = new URLRequest(text_xml_url);
			/*var obj:Object = new Object();
			if (User.getInstance().sessionID){
				obj.PHPSESSID = User.getInstance().sessionID;
			}
			urlRequest.data = obj;*/
			loader.load(urlRequest);
		}
		// Our reaction function
		private function onLoaded(e:Event) : void {
			debug ("onLoaded");
			textsXML = new XML(e.target.data);
			//debug (textsXML.toString());
			Texts.getInstance().ruTexts = getLangTextsAC (Texts.RU);
			Texts.getInstance().enTexts = getLangTextsAC (Texts.EN);
			Texts.getInstance().jaTexts = getLangTextsAC (Texts.JA);
			Texts.getInstance().zhTexts = getLangTextsAC (Texts.ZH);
			Texts.getInstance().setTexts();
			this.dispatchEvent( new AppEvent ( AppEvent.LOAD_TEXTS_COMPLETE ) );
		}
		
		private function getLangTextsAC ( lang : String ) : Array {
			//debug ("getLangTextsAC:" + lang);
			var texts:Array = new Array();
		//	var textsXMLLC:XMLListCollection = textsXML.childNodes;
			for each( var text:XML in textsXML..text){
				//debug (text.toString());
				//debug (text.@id.toString());
				//debug (text[lang].toString());
				texts[text.@id.toString()] = text[lang].toString();
			} 
			return texts;
		}
		
		private function onIOError ( event : IOErrorEvent ) : void {
			debug ("onIOError");
			this.dispatchEvent( new AppEvent ( AppEvent.LOAD_TEXTS_FAILED ) );
		}
		
		private function onSecurityError ( event : SecurityErrorEvent ) : void {
			debug ("onSecurityError");
			this.dispatchEvent( new AppEvent ( AppEvent.LOAD_TEXTS_FAILED ) );
		}
		
		private function debug ( str : String ) : void {
			if (Main.getInstance().debugger != null)
				Main.getInstance().debugger.text += str + "\n";
		}
	}
}