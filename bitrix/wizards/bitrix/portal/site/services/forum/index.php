<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("forum"))
	return;

$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

// Forum group
$arGroupID = Array(
	"GENERAL" => 0,
	"COMMENTS" => 0,
	"HIDDEN" => 0,
);

$dbExistsGroup = CForumGroup::GetListEx(array(), array("LID" => LANGUAGE_ID));
while ($arExistsGroup = $dbExistsGroup->Fetch())
{
	foreach ($arGroupID as $xmlID => $ID)
	{
		if ($arExistsGroup["NAME"] == GetMessage($xmlID."_GROUP_NAME") )
			$arGroupID[$xmlID] = $arExistsGroup["ID"];
	}
}

$sort = 1;
foreach ($arGroupID as $xmlID => $groupID)
{
	if ($groupID > 0)
		continue;

	$arNewGroup = Array("SORT" => $sort++, "LANG" => Array());
	foreach($arLanguages as $languageID)
	{
		$arMessages = WizardServices::IncludeServiceLang("index.php", $languageID, $bReturnArray=true);
		$arNewGroup["LANG"][] = Array(
			"LID" => $languageID,
			"NAME" => (array_key_exists($xmlID."_GROUP_NAME",$arMessages) ? $arMessages[$xmlID."_GROUP_NAME"] : GetMessage($xmlID."_GROUP_NAME")),
			"DESCRIPTION" => (array_key_exists($xmlID."_GROUP_DESCRIPTION",$arMessages) ? $arMessages[$xmlID."_GROUP_DESCRIPTION"] : GetMessage($xmlID."_GROUP_DESCRIPTION"))
		);
	}

	$arGroupID[$xmlID] = CForumGroup::Add($arNewGroup);
}

$rsUser = CUser::GetByID(1);
if ($arUser = $rsUser->Fetch())
{
	$userName = $arUser["NAME"].(strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0?"":" ").$arUser["LAST_NAME"];
	$authorName = (strlen(trim($userName)) > 0 ? $userName : $arUser["LOGIN"]);
}
else
	$authorName = "admin";

$arForums = Array(

	/*Array(
		"XML_ID" => "GENERAL",
		"NAME" => GetMessage("GENERAL_FORUM_NAME"),
		"DESCRIPTION" => GetMessage("GENERAL_FORUM_DECRIPTION"),
		"SORT" => 1,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "Y",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["GENERAL"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#",
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			"2" => "E",
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
			WIZARD_EMPLOYEES_GROUP => "M",
			WIZARD_PERSONNEL_DEPARTMENT_GROUP => "M",
		),
		"TOPICS" => Array(
			Array(
				"TITLE"			=> GetMessage("GENERAL_FORUM_TOPIC_TITLE"),
				"DESCRIPTION"	=> GetMessage("GENERAL_FORUM_TOPIC_DESCRIPTION"),
				"ICON_ID"		=> 0,
				"TAGS"			=> "",
				"USER_START_ID" => 1,
				"USER_START_NAME" => $authorName,
				"LAST_POSTER_NAME" => $authorName,
				"APPROVED" => "Y",
				"MESSAGES" => Array(
					Array(
						"POST_MESSAGE"	=> GetMessage("GENERAL_FORUM_MESSAGE_BODY"),
						"USE_SMILES"	=> "Y",
						"APPROVED"		=> "Y",
						"AUTHOR_NAME"	=> $authorName,
						"AUTHOR_EMAIL"	=> "",
						"AUTHOR_ID"		=> "1",
					),
				),
			),
		),
	),*/

	Array(
		"XML_ID" => "NEWS_COMMENTS",
		"NAME" => GetMessage("NEWS_COMMENTS_FORUM_NAME"),
		"DESCRIPTION" => GetMessage("NEWS_COMMENTS_FORUM_DESCRIPTION"),
		"SORT" => 2,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "Y",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"DEDUPLICATION" => "N",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["COMMENTS"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#"
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			"2" => "M",
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
		),
	),

	Array(
		"XML_ID" => "PHOTOGALLERY_COMMENTS",
		"NAME" => GetMessage("PHOTOGALLERY_COMMENTS_FORUM_NAME"),
		"DESCRIPTION" => GetMessage("PHOTOGALLERY_COMMENTS_FORUM_DESCRIPTION"),
		"SORT" => 3,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "Y",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"DEDUPLICATION" => "N",
		"FORUM_GROUP_ID" => $arGroupID["HIDDEN"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#"
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			"2" => "M",
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
		),
	),


	Array(
		"XML_ID" => "USERS_AND_GROUPS",
		"NAME" => GetMessage("USERS_AND_GROUPS_FORUM_NAME"),
		"DESCRIPTION" => GetMessage("USERS_AND_GROUPS_FORUM_DESCRIPTION"),
		"SORT" => 4,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "A",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"DEDUPLICATION" => "N",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["HIDDEN"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#"
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
		),
	),

	Array(
		"XML_ID" => "DOCS_SHARED_COMMENTS",
		"NAME" => GetMessage("DOCS_SHARED_COMMENTS_NAME"),
		"DESCRIPTION" => GetMessage("DOCS_SHARED_COMMENTS_DECRIPTION"),
		"SORT" => 5,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "Y",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"DEDUPLICATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["HIDDEN"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#",
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			"2" => "M",
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
		),
	),

	Array(
		"XML_ID" => "DOCS_SALES_COMMENTS",
		"NAME" => GetMessage("DOCS_SALES_COMMENTS_NAME"),
		"DESCRIPTION" => GetMessage("DOCS_SALES_COMMENTS_DECRIPTION"),
		"SORT" => 6,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "Y",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"DEDUPLICATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["HIDDEN"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#",
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			"2" => "A",
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
			WIZARD_EMPLOYEES_GROUP => "M",
			WIZARD_MARKETING_AND_SALES_GROUP => "M",
		),
	),

	Array(
		"XML_ID" => "DOCS_DIRECTORS_COMMENTS",
		"NAME" => GetMessage("DOCS_DIRECTORS_COMMENTS_NAME"),
		"DESCRIPTION" => GetMessage("DOCS_DIRECTORS_COMMENTS_DECRIPTION"),
		"SORT" => 6,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "Y",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"DEDUPLICATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["HIDDEN"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#",
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			"2" => "A",
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
			WIZARD_DIRECTION_GROUP => "M",
		),
	),
	Array(
		"XML_ID" => "GROUPS_AND_USERS_FILES_COMMENTS",
		"NAME" => GetMessage("GROUPS_AND_USERS_COMMENTS_NAME"),
		"DESCRIPTION" => GetMessage("GROUPS_AND_USERS_COMMENTS_DESCRIPTION"),
		"SORT" => 6,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "Y",
		"ALLOW_BIU" => "Y",
		"ALLOW_IMG" => "Y",
		"ALLOW_LIST" => "Y",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "Y",
		"ALLOW_FONT" => "Y",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "Y",
		"ALLOW_NL2BR" => "N",
		"MODERATION" => "N",
		"DEDUPLICATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["HIDDEN"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "N",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."community/forum/messages/forum#FORUM_ID#/topic#TOPIC_ID#/message#MESSAGE_ID#/#message#MESSAGE_ID#",
		),
		"EVENT1" => "forum",
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => Array(
			"2" => "M",
			WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y"
		),
	),
);

foreach ($arForums as $arForum)
{
	$dbForum = CForumNew::GetList(Array(), Array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => $arForum["XML_ID"]));
	if ($dbForum->Fetch())
		continue;

	$forumID = CForumNew::Add($arForum);
	if ($forumID < 1 || !isset($arForum["TOPICS"]) || !is_array($arForum["TOPICS"]) )
		continue;

	foreach ($arForum["TOPICS"] as $arTopic)
	{
		$arTopic["FORUM_ID"] = $forumID;
		$topicID = CForumTopic::Add($arTopic);

		if ($topicID < 1 || !isset($arTopic["MESSAGES"]) || !is_array($arTopic["MESSAGES"]) )
			continue;

		foreach ($arTopic["MESSAGES"] as $arMessage)
		{
			$arMessage["FORUM_ID"] = $forumID;
			$arMessage["TOPIC_ID"] = $topicID;

			$messageID = CForumMessage::Add($arMessage, false);
			if ($messageID < 1)
			{
				CForumTopic::Delete($topicID);
				continue 2;
			}
		}
		CForumTopic::SetStat($topicID);
	}
}

$fidParameter = "";
$dbForum = CForumNew::GetList(Array(), Array("SITE_ID" =>  WIZARD_SITE_ID));
while ($arForum = $dbForum->Fetch())
{
	if ($arForum["FORUM_GROUP_ID"] != $arGroupID["HIDDEN"] && $arForum["FORUM_GROUP_ID"] != $arGroupID["COMMENTS"])
		$fidParameter .= $arForum["ID"].",";
}
$fidParameter = rtrim($fidParameter, ",");

$forum_theme = 'blue';
switch(WIZARD_THEME_ID)
{
case 'green':
	$forum_theme = "green";
	break;
case 'red':
	$forum_theme = "red";
	break;
case 'brown':
	$forum_theme = "beige";
	break;
case 'lightblue':
	$forum_theme = "blue";
	break;
case 'dark-blue':
	$forum_theme = "blue";
	break;
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/community/forum.php", Array(
	"VISIBLE_FORUMS" => $fidParameter,
	"FORUM_THEME" => $forum_theme,
));

$newsForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => "NEWS_COMMENTS"));
if ($arRes = $dbRes->Fetch())
	$newsForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/about/life.php", Array("NEWS_COMMENTS_FORUM_ID" => $newsForumID));

COption::SetOptionString("intranet", "wizard_life_forum_id", intval($newsForumID), false, WIZARD_SITE_ID);

$photoCommentsForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("XML_ID" => "PHOTOGALLERY_COMMENTS"));
if ($arRes = $dbRes->Fetch())
	$photoCommentsForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/company/gallery/index.php", Array("PHOTOGALLERY_COMMENTS" => $photoCommentsForumID));


$generalForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => "GENERAL"));
if ($arRes = $dbRes->Fetch())
	$generalForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", Array("GENERAL_FORUM_ID" => $generalForumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/desktop.php", Array("GENERAL_FORUM_ID" => $generalForumID));

$DocsSharedForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => "DOCS_SHARED_COMMENTS"));
if ($arRes = $dbRes->Fetch())
	$DocsSharedForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/docs/shared/index.php", Array("SHARED_FILES_FORUM_ID" => $DocsSharedForumID));

$DocsSalesForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => "DOCS_SALES_COMMENTS"));
if ($arRes = $dbRes->Fetch())
	$DocsSalesForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/docs/sale/index.php", Array("SALE_FILES_FORUM_ID" => $DocsSalesForumID));

$DocsDirectorsForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => "DOCS_DIRECTORS_COMMENTS"));
if ($arRes = $dbRes->Fetch())
	$DocsDirectorsForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/docs/manage/index.php", Array("DIRECTORS_FILES_FORUM_ID" => $DocsDirectorsForumID));

$DocsSocnetCommentsForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => "GROUPS_AND_USERS_FILES_COMMENTS"));
if ($arRes = $dbRes->Fetch())
	$DocsSocnetCommentsForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/company/personal.php", Array("FILES_FORUM_ID" => $DocsSocnetCommentsForumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/stream/index.php", Array("FILES_FORUM_ID" => $DocsSocnetCommentsForumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/workgroups/index.php", Array("FILES_FORUM_ID" => $DocsSocnetCommentsForumID));

if (!WIZARD_IS_RERUN)
{
	$APPLICATION->SetGroupRight("forum", WIZARD_PORTAL_ADMINISTRATION_GROUP, "W");
}

if (!defined("ADDITIONAL_INSTALL"))
{
	COption::SetOptionString("forum", "SHOW_VOTES", "N");
	COption::SetOptionString("forum", "file_max_size", 10485760);
}
?>
