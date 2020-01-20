<!DOCTYPE xtml PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<?php
  $getJsonData = file_get_contents('eResourceList.json');
  $decodeJsonData = json_decode($getJsonData, true);
?>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>資料庫</title>
  <link rel="stylesheet" type="text/css" href="lib/style.css"/>
</head>
<body onload="init();">
  <section>
    <h1>資料庫列表</h1>
    <div id="databaseList">
      <div class="search-wrap">
        <div class="search-frame">
          <input type="text" class="search" placeholder="搜尋資源" />
        </div>
      </div>
      <div class="atoz-wrap">
        <div class="atoz-title">A to Z:</div>
        <div id="atozField" class="atoz-field"></div>
      </div>
      <div class="content-field">
        <table class="databaseList-table">
          <thead>
            <tr>
              <th class="sort" data-sort="id">編號</th>
              <th class="sort" data-sort="name">資源名稱</th>
              <th class="sort" data-sort="type">類型</th>
              <th class="sort" data-sort="city">語文</th>
            </tr>
          </thead>
          <tbody class="list" id="databaseList">
<?php
  foreach ($decodeJsonData['rows'] as $row) {
    echo '<tr>
            <td class="id" data-label="編號">'.$row['id'].'</td>
            <td class="name" data-label="資源名稱">
              <a href="'.$row['url'].'">'
                .$row['resourceName'].
              '</a>
            </td>
            <td class="type" data-label="類型">'.$row['type'].'</td>
            <td class="city" data-label="語文">'.$row['language'].'</td>
          </tr>';
  }
?>
          </tbody>
        </table>
        <aside>
          <ul class="subject-list">
            <h3>類型</h3>
            <li>
              <a href="javascript:searchBy('全文資料庫','type');">全文資料庫</a>
            </li>
            <li>
              <a href="javascript:searchBy('索摘資料庫','type');">索摘資料庫</a>
            </li>
            <li>
              <a href="javascript:searchBy('博碩士論文','type');">博碩士論文</a>
            </li>
            <li>
              <a href="javascript:searchBy('電子期刊','type');">電子期刊</a>
            </li>
            <li>
              <a href="javascript:searchBy('電子書','type');">電子書</a>
            </li>
          </ul>
          <ul class="subject-list">
            <h3>主題</h3>
            <li>
              <a href="javascript:searchBy('中文','city');">test</a>
            </li>
          </ul>
        </aside>
      </div>
    </div>
  </section>
  <div class="mask">
    test
  </div>
</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
<script type="text/javascript">
  async function init() {
    // create hyper link of a to z
    let englishAnchor = await createEnglishAnchor();
    document.getElementById("atozField").appendChild(englishAnchor);
  }
  function searchAtoZ(upperCharacter) {
    let lowCharater = upperCharacter.toLowerCase();
    // console.log(param);
    // contactList.search(param);
    contactList.filter(function(item) {
      // the item includes html tag to impact the result
      var regex = /(<([^>]+)>)/ig;
      removeTagResult = item.values().name.replace(regex, "").trim();
      console.log(removeTagResult);
      if (removeTagResult.charAt(0) === upperCharacter || removeTagResult.charAt(0) === lowCharater) {
        return true;
      } else {
        return false;
      }
    });
  }
  function searchBy(term, field) {
    contactList.search(term, [field]);
  }
  function searchAll() {
    contactList.filter();
  }
  function removeClickedClass() {

    element.classList.remove("mystyle");
  }
  var options = {
    valueNames: [ 'id', 'name', 'type', 'city' ],
    page: 500
  };
  function createEnglishAnchor() {
    return new Promise((resolve, reject) => {
      let linkWrap = document.createElement('div')
      linkWrap.className = 'link-field';

      let totalAnchor = document.createElement('a');
      let totalAnchorText = document.createTextNode('全部');
      totalAnchor.setAttribute('href', '#');
      totalAnchor.className = 'clicked';
      totalAnchor.addEventListener('click', searchAll, false);
      totalAnchor.appendChild(totalAnchorText);
      linkWrap.appendChild(totalAnchor);

      // let numberQuery = 'JN+0*+OR+JN+1*+OR+JN+2*+OR+JN+3*+OR+JN+4*+OR+JN+5*+OR+JN+6*+OR+JN+7*+OR+JN+8*+OR+JN+9*';
      // let numberAnchor = document.createElement('a');
      // let numberAnchorText = document.createTextNode('0 - 9');
      // numberAnchor.setAttribute('href', `#`);
      // numberAnchor.appendChild(numberAnchorText);
      // linkWrap.appendChild(numberAnchor);

      // for char A to Z
      for(let loop = 0; loop < 26; loop++) {
        let anchor = document.createElement('a');
        let alphabet = String.fromCharCode(65 + loop);
        let anchorText = document.createTextNode(alphabet);
        anchor.setAttribute('href', `#`);
        anchor.addEventListener('click', function(){ searchAtoZ(`${alphabet}`); }, false);
        anchor.appendChild(anchorText);
        linkWrap.appendChild(anchor);
      }

      resolve(linkWrap)
    })
  }
  // Init list
  var contactList = new List('databaseList', options);
//   .on("updated", function(list) {
//   var isFirst = list.i == 1;
//   var isLast = list.i > list.matchingItems.length - list.page;

//   // make the Prev and Nex buttons disabled on first and last pages accordingly
//   $(".pagination-prev.disabled, .pagination-next.disabled").removeClass(
//     "disabled"
//   );
//   if (isFirst) {
//     $(".pagination-prev").addClass("disabled");
//   }
//   if (isLast) {
//     $(".pagination-next").addClass("disabled");
//   }

//   // hide pagination if there one or less pages to show
//   if (list.matchingItems.length <= perPage) {
//     $(".pagination-wrap").hide();
//   } else {
//     $(".pagination-wrap").show();
//   }
// });

  var idField = $('#id-field'),
      nameField = $('#name-field'),
      typeField = $('#type-field'),
      cityField = $('#city-field'),
      addBtn = $('#add-btn'),
      editBtn = $('#edit-btn').hide();

  // Sets callbacks to the buttons in the list
  // refreshCallbacks();

  addBtn.click(function() {
    contactList.add({
      id: Math.floor(Math.random()*110000),
      name: nameField.val(),
      type: typeField.val(),
      city: cityField.val()
    });
    clearFields();
    // refreshCallbacks();
  });

  editBtn.click(function() {
    var item = contactList.get('id', idField.val())[0];
    item.values({
      id:idField.val(),
      name: nameField.val(),
      type: typeField.val(),
      city: cityField.val()
    });
    clearFields();
    editBtn.hide();
    addBtn.show();
  });

  // function refreshCallbacks() {
  //   // Needed to add new buttons to jQuery-extended object
  //   editBtns = $(editBtns.selector);
    
  //   editBtns.click(function() {
  //     var itemId = $(this).closest('tr').find('.id').text();
  //     var itemValues = contactList.get('id', itemId)[0].values();
  //     idField.val(itemValues.id);
  //     nameField.val(itemValues.name);
  //     ageField.val(itemValues.age);
  //     cityField.val(itemValues.city);
      
  //     editBtn.show();
  //     addBtn.hide();
  //   });
  // }

  function clearFields() {
    nameField.val('');
    typeField.val('');
    cityField.val('');
  }
</script>
