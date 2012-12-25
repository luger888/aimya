//////////////////////////////////
// VIDEO PLAYER v5--------------//
// VIDEOPLAYER CLASS -----------//
// SUPPORT : -------------------//
// contact@flashaman.com -------//
// bpaul3262@yahoo.com ---------//
//////////////////////////////////

package com.flashaman.videoplayer
{
	
	// - IMPORTS
	
	// - DISPLAY
	import flash.display.MovieClip;
	import flash.display.StageDisplayState;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	
	// - EVENTS
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.FullScreenEvent;
	import flash.events.KeyboardEvent;
	import flash.events.NetStatusEvent;
	
	// - ui
	import flash.ui.Keyboard;
	
	// - TEXT
	import flash.text.TextField;
	
	//- CAURINA
	import caurina.transitions.Tweener;
	import caurina.transitions.properties.FilterShortcuts;	 
	import caurina.transitions.properties.ColorShortcuts;
	
	//- MEDIA
	import flash.media.Video;
	import flash.media.SoundChannel;
	import flash.media.SoundChannel;
	import flash.media.SoundTransform;
	
	//- NET
    import flash.net.NetConnection;
    import flash.net.NetStream;
	
	//- UTILS
	import flash.utils.setInterval;
	import flash.utils.clearInterval;
	
	import flash.system.Capabilities;
	
	
	public class Videoplayer extends MovieClip
	{
		// -- VARIABLES
		
		private var _playerWidth:int = 600;
		private var _playerHeight:int = 320;
		private var _videoURL:String = "video/video1.flv";
		private var _imageURL:String = "image/1.jpg";
		private var _autoplay:Boolean = false;
		private var _themeColor:Object = 0x3c9bd6;
		private var _effectColor:Object = 0xffffff;
		private var _latestVolumeVal:Number = 0.5;//latest volume value - used for mute 
		private var _videoLoadedInterval:uint;//interval used to refresh the loaded video time
		private var _isSeek:Boolean= false;
		private var _title:String= "TITLE GOES HERE";
		private var _description:String= "SHORT DESCRIPTION";
		
		private var _infoEnabled:Boolean = true;
		private var _fsEnabled:Boolean = true;
		private var _volumeEnabled:Boolean = true;
		
		// -- CONST
		private var IMAGE:MovieClip;
		private var VIDEO:MovieClip;
		private var CONTROLS:MovieClip;
		
		public function Videoplayer()
		{
			addEventListener(Event.ENTER_FRAME, Init, false, 0, true);
		}
		
		private function Init(e:Event):void
		{
			removeEventListener(Event.ENTER_FRAME, Init);
			try
			{
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align = StageAlign.TOP_LEFT;
			}
			catch(e:Error)
			{
			}
			FilterShortcuts.init();
			ColorShortcuts.init();
			SetShortcuts();
			ReadFlashVars();
			
			SetProperties();
			//SetColors();
			SetEvents();
			SetVolume();
			VIDEO.Load(_videoURL);
			if(_autoplay)
			{
				VIDEO.Play();
				HidePlayButton(true);
			}
			IMAGE.Load(_imageURL);
			Tweener.addTween(CONTROLS, { y:_playerHeight,  time:1, delay:1, transition:"linear"});
			Tweener.addTween(mcPlayB, { alpha:0,   time:1,delay:1, transition:"linear"});
			_videoLoadedInterval = setInterval(UpdateLoader,30);
			return;
		}// close function
		
		private function SetShortcuts():void
		{
			IMAGE = new MovieClip();
			IMAGE = this.mcImage;
			VIDEO = new MovieClip();
			VIDEO = this.mcVideo;
			CONTROLS = new MovieClip();
			CONTROLS = this.mcControls;
			return;
		}// close function
		
		
		private function SetColors()
		{
			Tweener.addTween(mcDescription.mcTitle, { _color:_themeColor,  transition:"linear"});
			
			Tweener.addTween(CONTROLS.mcPlay.mcColor, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcInfo.mcColor, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcFS.mcColor, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcPause.mcColor, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcTime, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcTotal, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcVolume.mcBar, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcVolume.mcBack, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcSeek.mcBar, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcSeek.mcLoader.mcColor, { _color:_themeColor,  transition:"linear"});
			//Tweener.addTween(CONTROLS.mcSeek.mcBack, { _color:_themeColor,  transition:"linear"});
			Tweener.addTween(CONTROLS.mcSeek.mcScrub, { _color:_themeColor,  transition:"linear"});
			
		}
		
		private function ReadFlashVars():void
		{
			try {
			var paramObj:Object = loaderInfo.parameters;
			// read player width
			if (paramObj["width"]!=null) {
					_playerWidth = paramObj["width"];
			}
			// read player height
			if (paramObj["height"]!=null) {
					_playerHeight = paramObj["height"];
			}
			
			if (paramObj["videopath"]!=null){
				_videoURL = paramObj["videopath"];
			}
			
			if (paramObj["imagepath"]!=null){
				_imageURL = paramObj["imagepath"];
			}
			if (paramObj["color"]!=null)
			{
				_themeColor= paramObj["color"];
			}
			if (paramObj["volume"]!=null){
				_latestVolumeVal = paramObj["volume"];
			}
			if (paramObj["volumebutton"]!=null)
			{
				if (paramObj["volumebutton"] == "off")_volumeEnabled=false;
				if (paramObj["volumebutton"] == "on")_volumeEnabled=true;
			}
			if (paramObj["infobutton"]!=null)
			{
				if (paramObj["infobutton"] == "off")_infoEnabled=false;
				if (paramObj["infobutton"] == "on")_infoEnabled=true;
			}
			if (paramObj["fullscreenbutton"]!=null)
			{
				if (paramObj["fullscreenbutton"] == "off")_fsEnabled=false;
				if (paramObj["fullscreenbutton"] == "on")_fsEnabled=true;
			}
			if (paramObj["autoplay"] !=null){
				if(paramObj["autoplay"] == "true") _autoplay = true;
			}
			
			
			if (paramObj["titletext"]!=null)
			{
				_title= paramObj["titletext"];
			}
			if (paramObj["descriptiontext"]!=null)
			{
				_description= paramObj["descriptiontext"];
			}
			
			
			} catch (error:Error) {
			}
			return;
		}// CLOSE ReadFlashVars()
		
		private function SetProperties():void
		{
			var seekWidth:int =0;
			var posX:int = 0;
			
			this.mcBack.width = _playerWidth;
			this.mcBack.height = _playerHeight;
			this.mcBack.x = 0;
			this.mcBack.y = 0;
			
			this.mcMask.width = _playerWidth;
			this.mcMask.height = _playerHeight;
			this.mcMask.x = 0;
			this.mcMask.y = 0;
			this.mcPlayB.x = _playerWidth/2;
			this.mcPlayB.y = _playerHeight/2;
			this.mcCloseB.x = _playerWidth - 8;
			this.mcCloseB.y = 6;
			if(!_fsEnabled) CONTROLS.mcFS.visible = false;
			if(!_volumeEnabled) CONTROLS.mcVolume.visible = false;
			if(!_infoEnabled) CONTROLS.mcInfo.visible = false;
			
			IMAGE.Set(_playerWidth, _playerHeight);
			VIDEO.Set(_playerWidth, _playerHeight);
			CONTROLS.mcBack.width = _playerWidth;
			
			CONTROLS.x=0;
			CONTROLS.y=_playerHeight-CONTROLS.mcBack.height;
			
			seekWidth = _playerWidth - (16.5 + CONTROLS.mcPlay.width);
			if(_volumeEnabled) seekWidth = seekWidth - (8+ CONTROLS.mcVolume.mcBack.width);
			if(_infoEnabled) seekWidth = seekWidth - (12 + CONTROLS.mcInfo.width);
			if(_fsEnabled) seekWidth = seekWidth - (CONTROLS.mcFS.width);
			seekWidth = seekWidth - (CONTROLS.sep1.width + 10) - (CONTROLS.sep2.width + 6) - (CONTROLS.sep3.width + 26) - (CONTROLS.sep4.width + 11)
			seekWidth = seekWidth-(CONTROLS.mcTime.width + 6 +CONTROLS.mcTotal.width +6);

			posX+=8;
			CONTROLS.mcPlay.x = posX;
			CONTROLS.mcPause.x = posX;
			 posX+=CONTROLS.mcPlay.width + 16.5;
			CONTROLS.sep1.x =  posX;
			posX+=CONTROLS.sep1.width + 10;
			CONTROLS.mcInfo.x = posX;
			if(_infoEnabled) posX+= CONTROLS.mcInfo.width + 12;
			CONTROLS.sep2.x =  posX;
			posX+=CONTROLS.sep2.width + 6;
			CONTROLS.mcTime.x = posX;
			posX+=CONTROLS.mcTime.width + 6;
			CONTROLS.mcSeek.x = posX;
			CONTROLS.mcSeek.mcBack.width = seekWidth;
			CONTROLS.mcSeek.mcMask.width = seekWidth;
			CONTROLS.mcSeek.mcEvents.width = seekWidth;
			CONTROLS.mcSeek.mcLoader.width = seekWidth;
			CONTROLS.mcSeek.mcLoader.x = -CONTROLS.mcSeek.mcLoader.width;
			CONTROLS.mcSeek.mcBar.width = seekWidth;
			CONTROLS.mcSeek.mcBar.x = -CONTROLS.mcSeek.mcBar.width;
			CONTROLS.mcSeek.mcScrub.x = CONTROLS.mcSeek.mcBar.width + 3;
			CONTROLS.mcSeek.mcScrub.alpha = 0;
			posX+= CONTROLS.mcSeek.mcBack.width + 4;
			CONTROLS.mcTotal.x = posX;
			posX+=CONTROLS.mcTotal.width + 6;
			CONTROLS.sep3.x =  posX;
			posX+=CONTROLS.sep3.width + 26;
			CONTROLS.mcVolume.x = posX;
			if(_volumeEnabled) posX+= CONTROLS.mcVolume.mcBack.width + 8;
			CONTROLS.sep4.x =  posX;
			posX+=CONTROLS.sep4.width + 11;
			CONTROLS.mcFS.x = posX;
			mcDescription.mcBack.width = _playerWidth;
			mcDescription.mcTitle.width = _playerWidth-10;
			mcDescription.mcDescription.width = _playerWidth-10;
			mcDescription.mcTitle.htmlText = _title;
			mcDescription.mcDescription.htmlText = _description;
			mcDescription.y = -mcDescription.mcBack.height;
			mcDescription.mouseEnabled=false;
			
			HidePlayButton(false);
			
			this.alpha = 1;
			return;
		}// close function
		
		private function SetEvents()
		{
			
			this.addEventListener(MouseEvent.ROLL_OUT, OutPlayer);
			this.addEventListener(MouseEvent.ROLL_OVER, OverPlayer);
			stage.addEventListener(KeyboardEvent.KEY_UP, KeyPressed);//USED TO MANAGE KEYBOARD CONTROLS 
			stage.addEventListener(FullScreenEvent.FULL_SCREEN, FS);
			
			CONTROLS.mcPlay.buttonMode = true;
			CONTROLS.mcPlay.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			CONTROLS.mcPlay.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			CONTROLS.mcPlay.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
			
			mcPlayB.mcPlayB.buttonMode = true;
			mcPlayB.mcPlayB.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			mcPlayB.mcPlayB.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			mcPlayB.mcPlayB.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
			
			mcCloseB.buttonMode = true;
			mcCloseB.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			mcCloseB.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			mcCloseB.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
			
			CONTROLS.mcPause.buttonMode = true;
			CONTROLS.mcPause.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			CONTROLS.mcPause.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			CONTROLS.mcPause.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
			
			CONTROLS.mcInfo.buttonMode = true;
			CONTROLS.mcInfo.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			CONTROLS.mcInfo.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			CONTROLS.mcInfo.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
			
			CONTROLS.mcFS.buttonMode = true;
			CONTROLS.mcFS.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			CONTROLS.mcFS.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			CONTROLS.mcFS.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
			
			CONTROLS.mcVolume.buttonMode = true;
			CONTROLS.mcVolume.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			CONTROLS.mcVolume.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			CONTROLS.mcVolume.addEventListener(MouseEvent.MOUSE_DOWN, DownVolume);
			
			CONTROLS.mcSeek.buttonMode = true;
			CONTROLS.mcSeek.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			CONTROLS.mcSeek.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			CONTROLS.mcSeek.addEventListener(MouseEvent.MOUSE_DOWN, DownSeek);
			IMAGE.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
			VIDEO.addEventListener(MouseEvent.CLICK, ClickButton, false, 0, true);
		}
		
		private function KeyPressed(e:KeyboardEvent):void
		{
			var key:uint = e.keyCode;
			var step:uint = 5;
			switch (key) {
				case  Keyboard.SPACE:
				//txt.htmlText += key;
					VIDEO.Play();
					HidePlayButton(VIDEO.IsPlaying);
					break;
			}
			return;
			
		}
		
		private function FS(e:FullScreenEvent):void
		{
			if (stage.displayState == StageDisplayState.NORMAL) 
			{
				ArrangeObjects();
			}
			else
			{
				ArrangeObjectsFullScreen()
			}
			return;
		}
		
		private function ArrangeObjects()
		{
			
			this.addEventListener(MouseEvent.ROLL_OUT, OutPlayer);
			this.addEventListener(MouseEvent.ROLL_OVER, OverPlayer);
			Tweener.removeTweens(CONTROLS);
			var seekWidth:int =0;
			var posX:int = 0;
			this.mcBack.width = _playerWidth;
			this.mcBack.height = _playerHeight;
			this.mcBack.x = 0;
			this.mcBack.y = 0;
			
			this.mcMask.width = _playerWidth;
			this.mcMask.height = _playerHeight;
			this.mcMask.x = 0;
			this.mcMask.y = 0;
			
			
			this.mcPlayB.x = _playerWidth/2;
			this.mcPlayB.y = _playerHeight/2;
			this.mcCloseB.x = _playerWidth - 8;
			this.mcCloseB.y = 6;
			if(!_fsEnabled) CONTROLS.mcFS.visible = false;
			if(!_volumeEnabled) CONTROLS.mcVolume.visible = false;
			if(!_infoEnabled) CONTROLS.mcInfo.visible = false;
			CONTROLS.mcBack.width = _playerWidth;
			
			CONTROLS.x=0;
			CONTROLS.y=_playerHeight-CONTROLS.mcBack.height;
			/*seekWidth = _playerWidth - (16.5 + CONTROLS.mcPlay.width)// 8+ CONTROLS.mcTime.width + 8 +CONTROLS.mcTotal.width +8)
			if(_volumeEnabled) seekWidth = seekWidth - (8+ CONTROLS.mcVolume.mcBack.width);
			if(_infoEnabled) seekWidth = seekWidth - (12 + CONTROLS.mcInfo.width);
			if(_fsEnabled) seekWidth = seekWidth - (CONTROLS.mcFS.width);
			seekWidth = seekWidth - (CONTROLS.sep1.width + 10) - (CONTROLS.sep2.width + 6) - (CONTROLS.sep3.width + 26) - (CONTROLS.sep4.width + 11)
			seekWidth = seekWidth-(CONTROLS.mcTime.width + 6 +CONTROLS.mcTotal.width +6);*/
			seekWidth = _playerWidth - 145 - 120;
			posX+=8;
			CONTROLS.mcPlay.x = posX;
			CONTROLS.mcPause.x = posX;
			 posX+=CONTROLS.mcPlay.width + 16.5;
			CONTROLS.sep1.x =  posX;
			posX+=CONTROLS.sep1.width + 10;
			CONTROLS.mcInfo.x = posX;
			if(_infoEnabled) posX+= CONTROLS.mcInfo.width + 12;
			CONTROLS.sep2.x =  posX;
			posX+=CONTROLS.sep2.width + 6;
			CONTROLS.mcTime.x = posX;
			posX+=CONTROLS.mcTime.width + 6;
			CONTROLS.mcSeek.x = posX;
			CONTROLS.mcSeek.mcBack.width = seekWidth;
			CONTROLS.mcSeek.mcMask.width = seekWidth;
			CONTROLS.mcSeek.mcEvents.width = seekWidth;
			CONTROLS.mcSeek.mcLoader.width = seekWidth;
			CONTROLS.mcSeek.mcLoader.x = -CONTROLS.mcSeek.mcLoader.width;
			CONTROLS.mcSeek.mcBar.width = seekWidth;
			CONTROLS.mcSeek.mcBar.x = -CONTROLS.mcSeek.mcBar.width;
			CONTROLS.mcSeek.mcScrub.x = CONTROLS.mcSeek.mcBar.width + 3;
			CONTROLS.mcSeek.mcScrub.alpha = 0;
			posX+= CONTROLS.mcSeek.mcBack.width + 4;
			CONTROLS.mcTotal.x = posX;
			posX+=CONTROLS.mcTotal.width + 6;
			CONTROLS.sep3.x =  posX;
			posX+=CONTROLS.sep3.width + 26;
			CONTROLS.mcVolume.x = posX;
			if(_volumeEnabled) posX+= CONTROLS.mcVolume.mcBack.width + 8;
			CONTROLS.sep4.x =  posX;
			posX+=CONTROLS.sep4.width + 11;
			CONTROLS.mcFS.x = posX;
			trace(CONTROLS.mcFS.x)
			mcDescription.mcBack.width = _playerWidth;
			mcDescription.mcTitle.width = _playerWidth-10;
			mcDescription.mcDescription.width = _playerWidth-10;
			mcDescription.mcTitle.htmlText = _title;
			mcDescription.mcDescription.htmlText = _description;
			mcDescription.y = -mcDescription.mcBack.height;
			mcDescription.mouseEnabled=false;
			VIDEO.Set(_playerWidth, _playerHeight);
			VIDEO.mcHolder.scaleX = VIDEO.mcHolder.scaleY = 1;
			VIDEO.mcHolder.x = _playerWidth/2 - VIDEO.mcHolder.width/2;
			VIDEO.mcHolder.y = _playerHeight/2 - VIDEO.mcHolder.height/2;
			IMAGE.mcHolder.scaleY = IMAGE.mcHolder.scaleX = 1;
			CONTROLS.mcFS.gotoAndStop("simple");
		}
		
		private function ArrangeObjectsFullScreen()
		{
			this.removeEventListener(MouseEvent.ROLL_OUT, OutPlayer);
			this.removeEventListener(MouseEvent.ROLL_OVER, OverPlayer);
			Tweener.removeTweens(CONTROLS);
			var seekWidth:int =0;
			var posX:int = 0;
			this.mcBack.width = Capabilities.screenResolutionX;
			this.mcBack.height = Capabilities.screenResolutionY;
			this.mcBack.x = 0;
			this.mcBack.y = 0;
			
			this.mcMask.width = Capabilities.screenResolutionX;
			this.mcMask.height = Capabilities.screenResolutionY;
			this.mcMask.x = 0;
			this.mcMask.y = 0;
			this.mcPlayB.x =  Capabilities.screenResolutionX/2;
			this.mcPlayB.y = Capabilities.screenResolutionY/2;
			this.mcCloseB.x = Capabilities.screenResolutionX - 8;
			this.mcCloseB.y = 6;
			
			CONTROLS.mcFS.gotoAndStop("fs");
			
			CONTROLS.mcBack.width = Capabilities.screenResolutionX;
			
			CONTROLS.x=0;
			CONTROLS.y=Capabilities.screenResolutionY-CONTROLS.mcBack.height;
			/*seekWidth = Capabilities.screenResolutionX - (16.5 + CONTROLS.mcPlay.width + 12);
			if(_volumeEnabled) seekWidth = seekWidth - (8+ CONTROLS.mcVolume.mcBack.width);
			if(_infoEnabled) seekWidth = seekWidth - (12 + CONTROLS.mcInfo.width);
			if(_fsEnabled) seekWidth = seekWidth - (CONTROLS.mcFS.width + 18);
			seekWidth = seekWidth - (CONTROLS.sep1.width + 10) - (CONTROLS.sep2.width + 6) - (CONTROLS.sep3.width + 26) - (CONTROLS.sep4.width + 11)
			seekWidth = seekWidth-(CONTROLS.mcTime.width + 6 +4 + CONTROLS.mcTotal.width +6);*/
			seekWidth = Capabilities.screenResolutionX - 145 - 120;
			
			posX+=16.5;
			CONTROLS.mcPlay.x = posX;
			CONTROLS.mcPause.x = posX;
			
			posX+=CONTROLS.mcPlay.width + 12;
			CONTROLS.sep1.x =  posX;
			posX+=CONTROLS.sep1.width + 10;
			CONTROLS.mcInfo.x = posX;
			if(_infoEnabled) posX+= CONTROLS.mcInfo.width + 12;
			CONTROLS.sep2.x =  posX;
			posX+=CONTROLS.sep2.width + 6;
			CONTROLS.mcTime.x = posX;
			posX+=CONTROLS.mcTime.width + 6;
			CONTROLS.mcSeek.x = posX;
			CONTROLS.mcSeek.mcBack.width = seekWidth;
			CONTROLS.mcSeek.mcMask.width = seekWidth;
			CONTROLS.mcSeek.mcEvents.width = seekWidth;
			CONTROLS.mcSeek.mcLoader.width = seekWidth;
			CONTROLS.mcSeek.mcLoader.x = -CONTROLS.mcSeek.mcLoader.width;
			CONTROLS.mcSeek.mcBar.width = seekWidth;
			CONTROLS.mcSeek.mcBar.x = -CONTROLS.mcSeek.mcBar.width;
			CONTROLS.mcSeek.mcScrub.x = CONTROLS.mcSeek.mcBar.width + 3;
			CONTROLS.mcSeek.mcScrub.alpha = 0;
			posX+= CONTROLS.mcSeek.mcBack.width + 4;
			CONTROLS.mcTotal.x = posX;
			posX+=CONTROLS.mcTotal.width + 6;
			CONTROLS.sep3.x =  posX;
			posX+=CONTROLS.sep3.width + 26;
			CONTROLS.mcVolume.x = posX;
			if(_volumeEnabled) posX+= CONTROLS.mcVolume.mcBack.width + 8;
			CONTROLS.sep4.x =  posX;
			posX+=CONTROLS.sep4.width + 11;
			CONTROLS.mcFS.x = posX;
			mcDescription.mcBack.width = Capabilities.screenResolutionX;
			mcDescription.mcTitle.width = Capabilities.screenResolutionX-10;
			mcDescription.mcTitle.mouseEnabled = false;
			mcDescription.mcDescription.height = Capabilities.screenResolutionX-10;
			mcDescription.mcDescription.mouseEnabled = false;
			mcDescription.mouseEnabled=false;
			VIDEO.Set(Capabilities.screenResolutionX, Capabilities.screenResolutionY);
			var wm:Number = Capabilities.screenResolutionX / VIDEO.mcHolder.width;
			var hm:Number = Capabilities.screenResolutionY / VIDEO.mcHolder.height;
			
			if(wm>=hm)
			{
				
				VIDEO.mcHolder.width =Capabilities.screenResolutionX;
				VIDEO.mcHolder.scaleY =VIDEO.mcHolder.scaleX;
			}
			if(wm<hm)
			{
				
				VIDEO.mcHolder.height = Capabilities.screenResolutionY;
				VIDEO.mcHolder.scaleX = VIDEO.mcHolder.scaleY;
			}
			VIDEO.mcHolder.x = Capabilities.screenResolutionX/2 - VIDEO.mcHolder.width/2;
			VIDEO.mcHolder.y = Capabilities.screenResolutionY/2 - VIDEO.mcHolder.height/2;
			var wi:Number = Capabilities.screenResolutionX / IMAGE.mcHolder.width;
			var hi:Number = Capabilities.screenResolutionY / IMAGE.mcHolder.height;
			if(wi>hi)
			{
				IMAGE.mcHolder.width =Capabilities.screenResolutionX;
				IMAGE.mcHolder.scaleY =IMAGE.mcHolder.scaleX;
			}
			if(wi<hi)
			{
				IMAGE.mcHolder.height = Capabilities.screenResolutionY;
				IMAGE.mcHolder.scaleX = IMAGE.mcHolder.scaleY;
			}
		}
		
		
		private function OutPlayer(e:MouseEvent)
		{
			Tweener.removeTweens(CONTROLS);
			Tweener.removeTweens(mcPlayB);
			Tweener.addTween(CONTROLS, { y:this.mcBack.height,  time:1,delay:0.5, transition:"linear"});
			Tweener.addTween(mcPlayB, { alpha:0,   time:1,delay:0.5, transition:"linear"});
			
		}
		
		private function OverPlayer(e:MouseEvent)
		{
			Tweener.removeTweens(CONTROLS);
			Tweener.removeTweens(mcPlayB);
			Tweener.addTween(CONTROLS, { y:this.mcBack.height-CONTROLS.height,  time:0.5, transition:"linear"});
			Tweener.addTween(mcPlayB, { alpha:1,  time:0.5, transition:"linear"});

		}
		
		private function SetVolume():void
		{
			mcControls.mcVolume.mcBar.x = (_latestVolumeVal * 21) - 21;
			return;
		}
		
		private function OverButton(e:MouseEvent)
		{
			switch(e.currentTarget.name)
			{
				case "mcPlay":
				case "mcPause":
				case "mcFS":
				Tweener.addTween(e.currentTarget.mcColor, { _color:_effectColor,  time:.3, transition:"linear"});
				break;
				case "mcInfo":
					Tweener.addTween(e.currentTarget.mcColor, { _color:_effectColor,  time:.3, transition:"linear"});
					Tweener.addTween(mcDescription, { y:0,  time:.3, transition:"linear"});
				break;
				case "mcSeek":
					Tweener.addTween(e.currentTarget.mcScrub, { alpha:1,  time:.3, transition:"linear"});
				break;
				case "mcVolume":
					Tweener.addTween(e.currentTarget.mcBar, {_color:_effectColor,  time:.3, transition:"linear"});
				break;
			}
			
		}
		
		private function OutButton(e:MouseEvent)
		{
			
			switch(e.currentTarget.name)
			{
				case "mcPlay":
				case "mcPause":
				case "mcFS":
				Tweener.addTween(e.currentTarget.mcColor, { _color:_themeColor,  time:.3, transition:"linear"});
				break;
				case "mcInfo":
					Tweener.addTween(e.currentTarget.mcColor, { _color:_themeColor,  time:.3, transition:"linear"});
					Tweener.addTween(mcDescription, { y:-mcDescription.mcBack.height,  time:.6, transition:"linear"});
				break;
				case "mcSeek":
					Tweener.addTween(e.currentTarget.mcScrub, { alpha:0,  time:.3, transition:"linear"});
				break;
				case "mcVolume":
					Tweener.addTween(e.currentTarget.mcBar, {_color:_themeColor,  time:.3, transition:"linear"});
				break;
			}
			
		}
		
		private function ClickButton(e:MouseEvent)
		{
			switch(e.currentTarget.name)
			{
				case "mcPlay":
				case "mcPause":
				case "mcPlayB":
				case "mcVideo":
				case "mcImage":
					VIDEO.Play();
					HidePlayButton(VIDEO.IsPlaying);
				break;
				case "mcFS":
					//this.mcVideoHolder.removeEventListener(MouseEvent.CLICK, clickOnVideo);
					if (stage.displayState == StageDisplayState.NORMAL) 
					{			
						stage.displayState = StageDisplayState.FULL_SCREEN;			
					} 
					else 
					{
						stage.displayState = StageDisplayState.NORMAL;			
					}
				break;
				case "mcCloseB":
				// for turning off volume
					if(VIDEO.IsPlaying)
						VIDEO.Play();
					var evt:KeyboardEvent = new KeyboardEvent(KeyboardEvent.KEY_UP);
					evt.keyCode = Keyboard.ESCAPE;
					dispatchEvent(evt);
					break;
			}
		}
		
		private function HidePlayButton(flag:Boolean):void
		{
			if(flag)
			{
				CONTROLS.mcPlay.visible = false;
				mcPlayB.visible = false;
				CONTROLS.mcPause.visible = true;
				
			}
			else
			{
				mcPlayB.visible = true;
				CONTROLS.mcPlay.visible = true;
				CONTROLS.mcPause.visible = false;
			}
		}
		
		private function UpdateLoader()
		{
			
			CONTROLS.mcSeek.mcLoader.x=-CONTROLS.mcSeek.mcLoader.width + CONTROLS.mcSeek.mcLoader.width * (VIDEO.VideoLoadedTime) ;//-this.mcControls.mcSeek.mcLoader.width;
			
			if(VIDEO.IsPlaying)
			{
		   		CONTROLS.mcSeek.mcBar.x=Math.ceil((CONTROLS.mcSeek.mcBar.width) *(VIDEO.Time/VIDEO.TotalVideoTime))-CONTROLS.mcSeek.mcBar.width ;
				CONTROLS.mcSeek.mcScrub.x=CONTROLS.mcSeek.mcBar.x+CONTROLS.mcSeek.mcBar.width+1;
			}
			CONTROLS.mcTime.txt.htmlText=timeTransform(Math.floor( VIDEO.Time));
			CONTROLS.mcTotal.txt.htmlText = timeTransform(VIDEO.TotalVideoTime);
			
		}
		
		private function timeTransform(n:Number):String 
		{
			var minutes:String;
			var seconds:String;
			minutes= int(n/60).toString();
			seconds= int(n%60).toString();
			if (seconds.length<2) 
			{
				seconds=0+seconds;
			}
			if (minutes.length<2) 
			{
				minutes=0+minutes;
			}
			return minutes + ":" + seconds;
		}
		
		public function Check()
		{
			if(!VIDEO.IsPlaying)
			{
				
				HidePlayButton(VIDEO.IsPlaying);
				CONTROLS.mcSeek.mcBar.x = -CONTROLS.mcSeek.mcBar.width;
				CONTROLS.mcSeek.mcScrub.x = CONTROLS.mcSeek.mcBar.x + CONTROLS.mcSeek.mcBar.width + 3;
				
			}
		}
		
		private function DownSeek(e:MouseEvent)
		{
			this.removeEventListener(MouseEvent.ROLL_OUT, OutPlayer);
			this.removeEventListener(MouseEvent.ROLL_OVER, OverPlayer);
			CONTROLS.mcSeek.removeEventListener(MouseEvent.ROLL_OVER, OverButton);
			CONTROLS.mcSeek.removeEventListener(MouseEvent.ROLL_OUT, OutButton);
			VIDEO.IsSeek = true;
			if(VIDEO.IsPlaying)
			{
			VIDEO.Play();
			HidePlayButton(VIDEO.IsPlaying);
			clearInterval(_videoLoadedInterval);
			}
			//
			CONTROLS.mcSeek.mcBar.x=CONTROLS.mcSeek.mouseX-this.mcControls.mcSeek.mcLoader.width;//-this.mcControls.mcSeek.mcLoader.width+12;
			
			if(CONTROLS.mcSeek.mcBar.x>0) CONTROLS.mcSeek.mcBar.x=0;
			if(CONTROLS.mcSeek.mcBar.x<-CONTROLS.mcSeek.mcBar.width) CONTROLS.mcSeek.mcBar.x=-CONTROLS.mcSeek.mcBar.width;
			CONTROLS.mcSeek.mcScrub.x=CONTROLS.mcSeek.mcBar.x+CONTROLS.mcSeek.mcBar.width+1;
			stage.addEventListener(MouseEvent.MOUSE_UP, UpSeek);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, MoveSeek);
		}
		
		private function UpSeek(e:MouseEvent)
		{			
			VIDEO.IsSeek = false;			
			VIDEO.Seek=VIDEO.TotalVideoTime * (CONTROLS.mcSeek.mcBar.x+CONTROLS.mcSeek.mcBar.width)/(CONTROLS.mcSeek.mcBar.width);
			_videoLoadedInterval = setInterval(UpdateLoader,30);
			VIDEO.Play();
			HidePlayButton(VIDEO.IsPlaying);
			stage.removeEventListener(MouseEvent.MOUSE_UP, UpSeek);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, MoveSeek);
			CONTROLS.mcSeek.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			CONTROLS.mcSeek.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			if(CONTROLS.mcSeek.mouseX<0 || CONTROLS.mcSeek.mouseX > CONTROLS.mcSeek.mcBack.width || CONTROLS.mcSeek.mouseY<-3 || CONTROLS.mcSeek.mouseY>4)
			Tweener.addTween(CONTROLS.mcSeek.mcScrub, { alpha:0,  time:.33, transition:"linear"});
			this.addEventListener(MouseEvent.ROLL_OUT, OutPlayer);
			this.addEventListener(MouseEvent.ROLL_OVER, OverPlayer);
		}
		
		private function MoveSeek(e:MouseEvent)
		{
			CONTROLS.mcSeek.mcBar.x=CONTROLS.mcSeek.mouseX-this.mcControls.mcSeek.mcLoader.width;//-this.mcControls.mcSeek.mcLoader.width+12;
			if(CONTROLS.mcSeek.mcBar.x>0) CONTROLS.mcSeek.mcBar.x=0;
			if(CONTROLS.mcSeek.mcBar.x<-CONTROLS.mcSeek.mcBar.width) CONTROLS.mcSeek.mcBar.x=-CONTROLS.mcSeek.mcBar.width;
			CONTROLS.mcSeek.mcScrub.x=CONTROLS.mcSeek.mcBar.x+CONTROLS.mcSeek.mcBar.width+1;
			VIDEO.Seek=VIDEO.TotalVideoTime * (CONTROLS.mcSeek.mcBar.x+CONTROLS.mcSeek.mcBar.width)/(CONTROLS.mcSeek.mcBar.width);
		}
		
		private function DownVolume(e:MouseEvent):void
		{
			this.removeEventListener(MouseEvent.ROLL_OUT, OutPlayer);
			this.removeEventListener(MouseEvent.ROLL_OVER, OverPlayer);
			CONTROLS.mcVolume.mcBar.x = (CONTROLS.mcVolume.mouseX)-21;
			if(CONTROLS.mcVolume.mcBar.x > 0) mcControls.mcVolume.mcBar.x = 0;
			if(CONTROLS.mcVolume.mcBar.x < -21) mcControls.mcVolume.mcBar.x = -21;
			CONTROLS.mcVolume.removeEventListener(MouseEvent.ROLL_OUT, OutButton);
			CONTROLS.mcVolume.removeEventListener(MouseEvent.ROLL_OVER, OverButton);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, MoveVolume, false, 0 ,true);			
			stage.addEventListener(MouseEvent.MOUSE_UP, UpVolume);			
			_latestVolumeVal = (CONTROLS.mcVolume.mcBar.x + 21)/21;
			VIDEO.Volume = _latestVolumeVal;
		}
		
		private function MoveVolume(e:MouseEvent):void
		{
			CONTROLS.mcVolume.mcBar.x = (CONTROLS.mcVolume.mouseX)-21;
			if(CONTROLS.mcVolume.mcBar.x > 0) mcControls.mcVolume.mcBar.x = 0;
			if(CONTROLS.mcVolume.mcBar.x < -21) mcControls.mcVolume.mcBar.x = -21;
			_latestVolumeVal = (CONTROLS.mcVolume.mcBar.x + 21)/21;
			VIDEO.Volume = _latestVolumeVal;
		}
		
		private function UpVolume(e:MouseEvent):void
		{
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, MoveVolume);
			CONTROLS.mcVolume.addEventListener(MouseEvent.ROLL_OUT, OutButton, false, 0, true);
			CONTROLS.mcVolume.addEventListener(MouseEvent.ROLL_OVER, OverButton, false, 0, true);
			if(mcControls.mcVolume.mouseX<0 || mcControls.mcVolume.mouseX >21 || mcControls.mcVolume.mouseY<0 ||mcControls.mcVolume.mouseY>11)
			Tweener.addTween(mcControls.mcVolume.mcBar, { _color:_themeColor,  time:.33, transition:"linear"});
			this.addEventListener(MouseEvent.ROLL_OUT, OutPlayer);
			this.addEventListener(MouseEvent.ROLL_OVER, OverPlayer);
		}
		
	}// close class
}// close package