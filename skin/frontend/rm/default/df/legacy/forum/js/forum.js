var SIMPLE_FORUM_BACK_URL;
var SF_STORE_ID = 0;
function eraseSearchBlockSF(obj ,val, default_val)
{
	if(val == default_val)
	{
		obj.value = '';
	}
}

function restoreDefaultSF(obj ,val, default_val)
{
	if(val == '')
	{
		obj.value = default_val;
	}
}


function jumpToForumSF(url_end, url_begin, url_default)
{
	if(url_end != '') document.location.href = url_begin + url_end; 
	else document.location.href = url_begin + url_default;
}

function saveNickSF(_value)
{
	var id = 'rm-forum-nickname';
	if(!checkMageCookies())
	{
		return;
	}
	Mage.Cookies.set(id, _value);
}

function getNickSF(_do)
{
	var id        = 'rm-forum-nickname';
	var input_id  = 'NickName';
	if(!_do)
	{
		$(input_id).value = '';
		return;
	}
	if(!checkMageCookies())
	{
		return;
	}
	var nick = Mage.Cookies.get(id);
	if(nick) $(input_id).value = nick;
}

function checkMageCookies()
{
	if(!Mage.Cookies)
	{
		alert('Mage.Cookies are undefined! Disable foorum bookmarks System->Configuration->Simple Forum Configuration->Allow Bookmarks');
		return false;
	}
	else
	{
		var date = new Date();
		date.setTime(date.getTime()+(30*24*60*60*1000));
		Mage.Cookies.expires = date;
	}
	if(!Object.toJSON)
	{
		alert('Prototype Object.toJSON are undefined! Disable foorum bookmarks System->Configuration->Simple Forum Configuration->Allow Bookmarks');
		return false;
	}
	return true;
}

function showBookmarksSF(_u)
{
	var id = 'span.rm-forum-bookmark-items-class';
	if(_u)SIMPLE_FORUM_BACK_URL = _u;
	if(!checkMageCookies())
	{
		return;
	}
	var els = $$(id);
	var i   = getBookmarksSF(); 
	for(var u in els)
	{
		var el = els[u];
		if(el)
		{
			el.innerHTML = getBookmarksLength(i) ? addAsLinkBookmarksSF(getBookmarksLength(i), _u) : '0';
			if(i)
			{
				setBookmarksFormHiddenSF( i );
			}
		}
	}
 }
 
 function addFiledsSF(form, _id, _limit, _page)
 {
	form.innerHTML += '<input type="hidden" name="bookmark_forum[_id][' + _id + ']" value="'+_id+'" />';
 	form.innerHTML += '<input type="hidden" name="bookmark_forum[limit][' + _id + ']" value="'+_limit+'" />';
 	form.innerHTML += '<input type="hidden" name="bookmark_forum[_page][' + _id + ']" value="'+_page+'" />';
 }

 function setBookmarksFormHiddenSF(_obj)
 {
 	var id_form    = 'rm-forum-bookmarked-items-form';
	var keys       = Object.keys( _obj );
	var form       = $('' + id_form);
	if(!form) return;
	form.innerHTML = '';
	for(var i= 0; keys.length > i; i++)
	{
		var key = keys[i];
		var o   = _obj[key];
		addFiledsSF(form, o.id, o.limit, o.page)
	}
 }
 
 function getBookmarksLength(_obj)
 {
	if(_obj)
	{
		return Object.keys(_obj).length;
	}
 }
 
function submitBookmarksFormSF(url)
{
	var id_form = 'rm-forum-bookmarked-items-form';
	var form    = $(id_form);
	form.innerHTML += '<input type="hidden" name="___action" value="view" />';
	form.innerHTML += '<input type="hidden" name="redirect" value="'+url+'" />';
	form.submit(); 
}

function addAsLinkBookmarksSF(_n, _u)
{
	if(!_u){ _u = SIMPLE_FORUM_BACK_URL;}
	var a_link_begin = '<a href="javascript:void(0);" onclick="submitBookmarksFormSF(\''+_u+'\')" >';
	var a_link_end   = '</a>';
	return a_link_begin + _n + a_link_end;
}

function getBookmarksSF()
{
	var id = 'bookmarked_items_SF';
	if(SF_STORE_ID)
	{
		id += '_' + SF_STORE_ID;
	}
	if(!checkMageCookies())
	{
		return;
	}
	var items = Mage.Cookies.get(id);
	var obj   = items ? items.evalJSON() : null;
	return obj;
}

function setBookmarkSF(_id, _limit, _page)
{
	var i = getBookmarksSF();
	if(!i)
	{
		i = {};
	}
	i[_id] = {};
	i[_id]['id']    = _id;
	i[_id]['limit'] = _limit;
	i[_id]['page']  = _page;
	
	setBookmarkItems(i, true);
}

function setBookmarkItems(_obj, _do_update)
{
	var id  = 'bookmarked_items_SF';
	if(SF_STORE_ID)
	{
		id += '_' + SF_STORE_ID;
	}
	var str = Object.toJSON(_obj);
	Mage.Cookies.set(id, str);
	if(_do_update)
	{
		showBookmarksSF();
	}
}

function deleteBookmarkSF(_id)
{
	deleteRowBookmarkSF(_id);
	deleteCookieSF(_id);
}

function deleteCookieSF(_id)
{
	if(!checkMageCookies())
	{
		return;
	}
	var i = getBookmarksSF();
	if(i)
	{
		delete i[ _id ];
		setBookmarkItems(i, true);
	}
}

function deleteRowBookmarkSF(_id)
{
	var id_table = 'forum_table_bookmark';
	var id_row_b = 'rm-forum-row-id-';
	
	var table    = $(id_table);
	var row      = $(id_row_b + _id);
	table.deleteRow(row.rowIndex);
}

function eraseBookmarksSF()
{
	var obj = {};
	setBookmarkItems(obj);
}

function forumFastReplaySF()
{
	var _id_fast_replay = 'rm-forum-fast-reply-block';
	var el = $(_id_fast_replay);
	if(el && el.style)
	{
		if(el.style.display == 'block')
		{
			 el.style.display = 'none';
		}
		else 
		{
			el.style.display = 'block';
		}
	}
}