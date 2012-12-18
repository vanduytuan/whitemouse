/*
 DHTML Menu version 3.3.19
 Written by Andy Woolley
 Copyright 2002 (c) Milonic Solutions. All Rights Reserved.
 Plase vist http://www.milonic.co.uk/menu or e-mail menu3@milonic.com
 You may use this menu on your web site free of charge as long as you place prominent links to http://www.milonic.co.uk/menu and
 your inform us of your intentions with your URL AND ALL copyright notices remain in place in all files including your home page
 Comercial support contracts are available on request if you cannot comply with the above rules.
 This script featured on Dynamic Drive (http://www.dynamicdrive.com)
 */

//The following line is critical for menu operation, and MUST APPEAR ONLY ONCE. If you have more than one menu_array.js file rem out this line in subsequent files
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<script language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/script>";_d.write(mt)}
//Please leave the above line intact. The above also needs to be enabled if it not already enabled unless this file is part of a multi pack.

////////////////////////////////////
// Editable properties START here //
////////////////////////////////////

// Special effect string for IE5.5 or above please visit http://www.milonic.co.uk/menu/filters_sample.php for more filters
if(navigator.appVersion.indexOf("MSIE 6.0")>0)
{
	effect = "Fade(duration=0.2);Alpha(style=0,opacity=88);Shadow(color='#777777', Direction=135, Strength=5)"
}
else
{
	effect = "Shadow(color='#777777', Direction=135, Strength=5)" // Stop IE5.5 bug when using more than one filter
}


timegap=500				// The time delay for menus to remain visible
followspeed=5			// Follow Scrolling speed
followrate=40			// Follow Scrolling Rate
suboffset_top=10;		// Sub menu offset Top position 
suboffset_left=10;		// Sub menu offset Left position

style1=[				// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"black",					// Mouse Off Font Color
"feffed",				// Mouse Off Background Color
"red",				// Mouse On Font Color
"d8d7e8",				// Mouse On Background Color
"eeeeee",				// Menu Border Color 
11,						// Font Size in pixels
"normal",				// Font Style (italic or normal)
"normal",					// Font Weight (bold or normal)
"Verdana, Arial",		// Font Name
5,						// Menu Item Padding
"",			// Sub Menu Image (Leave this blank if not needed)
2,						// 3D Border & Separator bar
"b20000",				// 3D High Color
"ffffff",				// 3D Low Color
"blue",				// Current Page Item Font Color (leave this blank to disable)
"white",					// Current Page Item Background Color (leave this blank to disable)
"",			// Top Bar image (Leave this blank to disable)
"ffffff",				// Menu Header Font Color (Leave blank if headers are not needed)
"000099",				// Menu Header Background Color (Leave blank if headers are not needed)
]

addmenu(menu=[		// This is the array that contains your menu properties and details
"mainmenu",			// Menu Name - This is needed in order for the menu to be called
96,					// Menu Top - The Top position of the menu in pixels
60,				// Menu Left - The Left position of the menu in pixels
,					// Menu Width - Menus width in pixels
2,					// Menu Border Width 
,					// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
style1,				// Properties Array - this is set higher up, as above
1,					// Always Visible - allows the menu item to be visible at all time (1=on/0=off)
"left",				// Alignment - sets the menu elements text alignment, values valid here are: left, right or center
effect,				// Filter - Text variable for setting transitional effects on menu activation - see above for more info
,					// Follow Scrolling - Tells the menu item to follow the user down the screen (visible at all times) (1=on/0=off)
1, 					// Horizontal Menu - Tells the menu to become horizontal instead of top to bottom style (1=on/0=off)
,					// Keep Alive - Keeps the menu visible until the user moves over another menu or clicks elsewhere on the page (1=on/0=off)
,					// Position of TOP sub image left:center:right
,					// Set the Overall Width of Horizontal Menu to 100% and height to the specified amount (Leave blank to disable)
,					// Right To Left - Used in Hebrew for example. (1=on/0=off)
0,					// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,					// ID of the div you want to hide on MouseOver (useful for hiding form elements)
,					// Reserved for future use
,					// Reserved for future use
,					// Reserved for future use
// "Description Text", "URL", "Alternate URL", "Status", "Separator Bar"
,"&nbsp;Home&nbsp;","show-menu=about","/index_beta.php","",0 
,"&nbsp;Documentation&nbsp;","show-menu=documentation","/doc","",0
,"&nbsp;Populate&nbsp;","show-menu=populate","/populate","",0 
,"&nbsp;Precursors&nbsp;","show-menu=precursors","/precursor","",0
,"&nbsp;Links&nbsp;","show-menu=links",,"",0
,"&nbsp;Observatories&nbsp;","show-menu=observatories","http://www.wovo.org","",0
,"&nbsp;Contact&nbsp;us&nbsp;","show-menu=contact","javascript:linkTo_UnCryptMailto('ocknvq<tfrwtdqBpvw0gfw0ui', 2);","",0
])

	addmenu(menu=["about",
	,,65,1,"",style1,,"left",effect,,,,,,,,,,,,
	,"About","/about",,,0
	,"Mission","/about/mission.php",,,0
	,"First Idea","/about/idea.php",,,0
	,"History","/about/history.php",,,0
	])
		
	addmenu(menu=["documentation",
	,,95,1,"",style1,,"left",effect,,,,,,,,,,,,
	,"Database","/doc/database",,,0
	,"Upload system","/doc/system",,,0
	])
		
	addmenu(menu=["populate",
	,,90,1,"",style1,,"left",effect,,,,,,,,,,,,
	,"Convert","/populate/convert_new1.php",,,0
	,"Upload","/populate/upload.php",,,0
	,"My account","/populate/my_account.php",,,0
	])
		
	addmenu(menu=["precursors",
	,,90,1,"",style1,,"left",effect,,,,,,,,,,,,
	,"Single","/precursor/single.php",,,0
	,"Multiple","/precursor/multiple.php",,,0
	,"Analysis","/precursor/analysis.php",,,0
	])

	addmenu(menu=["links",
	,,110,1,"",style1,,"",effect,,,,,,,,,,,,
	,"<img src=iavcei_icon.gif border=0>&nbsp;IAVCEI", "http://www.iavcei.org",,,0
	,"<img src=wovo_icon.gif border=0>&nbsp;WOVO", "http://www.wovo.org",,,0
	,"<img src=gvp_icon.gif border=0>&nbsp;Smithsonian Inst.", "http://www.si.edu",,,0
	,"<img src=aist_icon.gif border=0>&nbsp;GSJ", "http://www.gsj.jp",,,0
	,"<img src=divo_icon.gif border=0>&nbsp;DIVO", "http://www.divo.org.it",,,0
	,"<img src=nied_icon.gif border=0>&nbsp;NIED", "http://www.bosai.go.jp/e/",,,0
	,"<img src=vogripa_icon.gif border=0>&nbsp;VOGRIPA", "http://www.vogripa.bris.uk",,,0
	,"<img src=iris_icon.gif border=0>&nbsp;IRIS", "http://www.iris.org",,,0
	,"<img src=unavco_icon.gif border=0>&nbsp;UNAVCO", "http://www.unavco.org",,,0
	])

	addmenu(menu=["observatories",
	,,110,1,"",style1,,"",effect,,,,,,,,,,,,
	,"<img src=usgs_icon.gif border=0>&nbsp;USGS", "http://volcanoes.usgs.gov",,,0
	,"<img src=cvo_icon.gif border=0>&nbsp;CVO", "http://vulcan.wr.usgs.gov",,,0
	,"<img src=hvo_icon.gif border=0>&nbsp;HVO", "http://hvo.wr.usgs.gov",,,0
	,"<img src=avo_icon.gif border=0>&nbsp;AVO", "http://www.avo.alaska.edu",,,0
	,"<img src=geonet_icon.gif border=0>&nbsp;GEONET", "http://www.geonet.org.nz",,,0
	,"<img src=ingv_icon.gif border=0>&nbsp;INGV", "http://www.ingv.it",,,0
	,"<img src=sakurajima_icon.gif border=0>&nbsp;Sakurajima", "http://www.dpri.kyoto-u.ac.jp/~kazan/default_e.html",,,0
	])


dumpmenus()
