<?php
$NICHE_NAV_ACTIVE = 'new';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Audit — Pick State & City — hub.niche</title>
<style>
  body { font-family: -apple-system, Segoe UI, sans-serif; background: #f5f6fa; margin: 0; color: #1e1e2e; }
  .wrap { max-width: 900px; margin: 0 auto; padding: 32px 20px; }
  .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 20px; }
  h1 { font-size: 20px; margin-bottom: 4px; }
  .sub { color: #666; margin-bottom: 20px; font-size: 14px; }
  label { display: block; font-size: 13px; font-weight: 600; margin: 14px 0 4px; }
  select, input[type=text], input[type=number] { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; background:#fff; }
  select:disabled { background: #f5f5f7; color: #999; }
  .row { display: flex; gap: 16px; }
  .row > div { flex: 1; }
  .btn { background: #4f46e5; color: #fff; border: none; padding: 11px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 18px; }
  .btn:hover:not(:disabled) { background: #4338ca; }
  .btn:disabled { opacity: .5; cursor: not-allowed; }
  .note { font-size:12px; color:#999; margin-top:10px; }
  .loading-tag { font-weight:400; color:#4f46e5; font-size:12px; display:none; }
  .using-tag { font-weight:400; color:#166534; font-size:12px; }
</style>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<div class="wrap">
  <h1>Step 1 — Pick State &amp; City</h1>
  <div class="sub">Pick a state, then a major city — instant, no lookup needed. Surrounding small cities auto-populate once you pick a main city; cities under ~100k population are usually the easiest targets.</div>

  <div class="card">
    <div class="row">
      <div>
        <label>State</label>
        <select id="stateSelect" onchange="onStateChange()">
          <option value="">Select state...</option>
        </select>
      </div>
      <div>
        <label>Main City</label>
        <select id="citySelect" onchange="onCityChange()" disabled>
          <option value="">Select city...</option>
        </select>
      </div>
      <div>
        <label>Surrounding Small City <span class="loading-tag" id="nearbyLoading">(searching...)</span></label>
        <select id="nearbySelect" onchange="onNearbyChange()" disabled>
          <option value="">Use main city above...</option>
        </select>
      </div>
      <div>
        <label>Population <span class="using-tag" id="popTag"></span></label>
        <input type="number" id="population" placeholder="e.g. 148000">
      </div>
    </div>

    <button type="button" class="btn" id="continueBtn" disabled>Continue → Niche Details</button>
    <div class="note">Surrounding-city list uses OpenStreetMap data — population figures are estimates where available and should be spot-checked against Census data before you commit real budget. Picking a small city here overrides the main city for the audit.</div>
  </div>
</div>

<script>
// Top 10 cities per state — instant, no API call (same pattern as the Prospecting Manager tool)
const TOP_CITIES = {"Alabama":["Birmingham","Montgomery","Huntsville","Mobile","Tuscaloosa","Hoover","Dothan","Auburn","Decatur","Madison"],"Alaska":["Anchorage","Fairbanks","Juneau","Sitka","Ketchikan","Wasilla","Kenai","Kodiak","Bethel","Palmer"],"Arizona":["Phoenix","Tucson","Mesa","Chandler","Scottsdale","Glendale","Gilbert","Tempe","Peoria","Surprise"],"Arkansas":["Little Rock","Fort Smith","Fayetteville","Springdale","Jonesboro","North Little Rock","Conway","Rogers","Pine Bluff","Bentonville"],"California":["Los Angeles","San Diego","San Jose","San Francisco","Fresno","Sacramento","Long Beach","Oakland","Bakersfield","Anaheim"],"Colorado":["Denver","Colorado Springs","Aurora","Fort Collins","Lakewood","Thornton","Arvada","Westminster","Pueblo","Centennial"],"Connecticut":["Bridgeport","New Haven","Stamford","Hartford","Waterbury","Norwalk","Danbury","New Britain","Bristol","Meriden"],"Delaware":["Wilmington","Dover","Newark","Middletown","Smyrna","Milford","Seaford","Georgetown","Elsmere","New Castle"],"Florida":["Jacksonville","Miami","Tampa","Orlando","St. Petersburg","Hialeah","Tallahassee","Fort Lauderdale","Port St. Lucie","Cape Coral"],"Georgia":["Atlanta","Augusta","Columbus","Macon","Savannah","Athens","Sandy Springs","Roswell","Johns Creek","Albany"],"Hawaii":["Honolulu","East Honolulu","Pearl City","Hilo","Kailua","Waipahu","Kaneohe","Mililani Town","Kahului","Ewa Gentry"],"Idaho":["Boise","Meridian","Nampa","Idaho Falls","Pocatello","Caldwell","Coeur d'Alene","Twin Falls","Lewiston","Post Falls"],"Illinois":["Chicago","Aurora","Joliet","Rockford","Springfield","Elgin","Peoria","Champaign","Waukegan","Cicero"],"Indiana":["Indianapolis","Fort Wayne","Evansville","South Bend","Carmel","Fishers","Bloomington","Hammond","Gary","Lafayette"],"Iowa":["Des Moines","Cedar Rapids","Davenport","Sioux City","Iowa City","Waterloo","Council Bluffs","Ames","West Des Moines","Dubuque"],"Kansas":["Wichita","Overland Park","Kansas City","Topeka","Olathe","Lawrence","Shawnee","Manhattan","Lenexa","Salina"],"Kentucky":["Louisville","Lexington","Bowling Green","Owensboro","Covington","Richmond","Georgetown","Florence","Hopkinsville","Nicholasville"],"Louisiana":["New Orleans","Baton Rouge","Shreveport","Metairie","Lafayette","Lake Charles","Kenner","Bossier City","Monroe","Alexandria"],"Maine":["Portland","Lewiston","Bangor","South Portland","Auburn","Biddeford","Sanford","Augusta","Saco","Westbrook"],"Maryland":["Baltimore","Columbia","Germantown","Silver Spring","Waldorf","Glen Burnie","Ellicott City","Frederick","Dundalk","Rockville"],"Massachusetts":["Boston","Worcester","Springfield","Lowell","Cambridge","New Bedford","Brockton","Quincy","Lynn","Fall River"],"Michigan":["Detroit","Grand Rapids","Warren","Sterling Heights","Ann Arbor","Lansing","Flint","Dearborn","Livonia","Troy"],"Minnesota":["Minneapolis","St. Paul","Rochester","Duluth","Bloomington","Brooklyn Park","Plymouth","St. Cloud","Eagan","Woodbury"],"Mississippi":["Jackson","Gulfport","Southaven","Hattiesburg","Biloxi","Meridian","Tupelo","Greenville","Olive Branch","Horn Lake"],"Missouri":["Kansas City","St. Louis","Springfield","Columbia","Independence","Lee's Summit","O'Fallon","St. Joseph","St. Charles","Blue Springs"],"Montana":["Billings","Missoula","Great Falls","Bozeman","Butte","Helena","Kalispell","Havre","Anaconda","Miles City"],"Nebraska":["Omaha","Lincoln","Bellevue","Grand Island","Kearney","Fremont","Hastings","North Platte","Norfolk","Columbus"],"Nevada":["Las Vegas","Henderson","Reno","North Las Vegas","Sparks","Carson City","Fernley","Elko","Mesquite","Boulder City"],"New Hampshire":["Manchester","Nashua","Concord","Derry","Dover","Rochester","Salem","Merrimack","Hudson","Londonderry"],"New Jersey":["Newark","Jersey City","Paterson","Elizabeth","Edison","Woodbridge","Lakewood","Toms River","Hamilton","Trenton"],"New Mexico":["Albuquerque","Las Cruces","Rio Rancho","Santa Fe","Roswell","Farmington","South Valley","Clovis","Hobbs","Alamogordo"],"New York":["New York City","Buffalo","Rochester","Yonkers","Syracuse","Albany","New Rochelle","Mount Vernon","Schenectady","Utica"],"North Carolina":["Charlotte","Raleigh","Greensboro","Durham","Winston-Salem","Fayetteville","Cary","Wilmington","High Point","Concord"],"North Dakota":["Fargo","Bismarck","Grand Forks","Minot","West Fargo","Williston","Dickinson","Mandan","Jamestown","Wahpeton"],"Ohio":["Columbus","Cleveland","Cincinnati","Toledo","Akron","Dayton","Parma","Canton","Youngstown","Lorain"],"Oklahoma":["Oklahoma City","Tulsa","Norman","Broken Arrow","Lawton","Edmond","Moore","Midwest City","Enid","Stillwater"],"Oregon":["Portland","Eugene","Salem","Gresham","Hillsboro","Beaverton","Bend","Medford","Springfield","Corvallis"],"Pennsylvania":["Philadelphia","Pittsburgh","Allentown","Erie","Reading","Scranton","Bethlehem","Lancaster","Harrisburg","York"],"Rhode Island":["Providence","Cranston","Warwick","Pawtucket","East Providence","Woonsocket","Coventry","Cumberland","North Providence","South Kingstown"],"South Carolina":["Columbia","Charleston","North Charleston","Mount Pleasant","Rock Hill","Greenville","Summerville","Goose Creek","Hilton Head","Sumter"],"South Dakota":["Sioux Falls","Rapid City","Aberdeen","Brookings","Watertown","Mitchell","Yankton","Pierre","Huron","Vermillion"],"Tennessee":["Memphis","Nashville","Knoxville","Chattanooga","Clarksville","Murfreesboro","Franklin","Jackson","Johnson City","Bartlett"],"Texas":["Houston","San Antonio","Dallas","Austin","Fort Worth","El Paso","Arlington","Corpus Christi","Plano","Laredo"],"Utah":["Salt Lake City","West Valley City","Provo","West Jordan","Orem","Sandy","Ogden","St. George","Layton","South Jordan"],"Vermont":["Burlington","South Burlington","Rutland","Barre","Montpelier","Winooski","St. Albans","Newport","Vergennes","Middlebury"],"Virginia":["Virginia Beach","Norfolk","Chesapeake","Richmond","Newport News","Alexandria","Hampton","Roanoke","Portsmouth","Suffolk"],"Washington":["Seattle","Spokane","Tacoma","Vancouver","Bellevue","Kent","Everett","Renton","Spokane Valley","Kirkland"],"West Virginia":["Charleston","Huntington","Parkersburg","Morgantown","Wheeling","Weirton","Fairmont","Martinsburg","Beckley","Clarksburg"],"Wisconsin":["Milwaukee","Madison","Green Bay","Kenosha","Racine","Appleton","Waukesha","Oshkosh","Eau Claire","Janesville"],"Wyoming":["Cheyenne","Casper","Laramie","Gillette","Rock Springs","Sheridan","Green River","Evanston","Riverton","Jackson"]};

const STATES = Object.keys(TOP_CITIES);
const stateEl = document.getElementById('stateSelect');
const cityEl = document.getElementById('citySelect');
const nearbyEl = document.getElementById('nearbySelect');
const popEl = document.getElementById('population');
const popTag = document.getElementById('popTag');
let nearbyPopMap = {}; // name -> population, refreshed per main city

STATES.forEach(s => stateEl.innerHTML += `<option value="${s}">${s}</option>`);

function onStateChange() {
  const s = stateEl.value;
  cityEl.innerHTML = '<option value="">Select city...</option>';
  if (s && TOP_CITIES[s]) TOP_CITIES[s].forEach(c => cityEl.innerHTML += `<option value="${c}">${c}</option>`);
  cityEl.disabled = !s;
  resetNearby();
  popEl.value = '';
  popTag.textContent = '';
  updateContinueBtn();
}

function resetNearby() {
  nearbyEl.innerHTML = '<option value="">Use main city above...</option>';
  nearbyEl.disabled = true;
  nearbyPopMap = {};
}

async function onCityChange() {
  popEl.value = '';
  popTag.textContent = '';
  updateContinueBtn();

  const state = stateEl.value;
  const city = cityEl.value;
  resetNearby();
  if (!state || !city) return;

  const loadingTag = document.getElementById('nearbyLoading');
  loadingTag.style.display = 'inline';

  try {
    const resp = await fetch('nearby-cities.php?state=' + encodeURIComponent(state) + '&city=' + encodeURIComponent(city));
    const rawText = await resp.text();
    console.log('nearby-cities raw response:', rawText);
    let data;
    try {
      data = JSON.parse(rawText);
    } catch (parseErr) {
      loadingTag.style.display = 'none';
      nearbyEl.innerHTML = '<option value="">Server returned invalid response — check console</option>';
      nearbyEl.disabled = true;
      return;
    }
    console.log('nearby-cities debug:', data.debug || data);
    loadingTag.style.display = 'none';

    if (data.error) {
      nearbyEl.innerHTML = '<option value="">' + data.error.slice(0, 60) + ' — see console</option>';
      nearbyEl.disabled = true;
      return;
    }
    if (!data.places || data.places.length === 0) {
      nearbyEl.innerHTML = '<option value="">No nearby cities found (0 results) — use main city above</option>';
      nearbyEl.disabled = true;
      return;
    }

    nearbyEl.innerHTML = '<option value="">Use main city above...</option>';
    data.places.forEach(p => {
      const popLabel = p.population ? `${Number(p.population).toLocaleString()} pop` : 'pop unknown';
      const sweetTag = (p.population && p.population <= 100000) ? ' ★ sweet spot' : '';
      const opt = document.createElement('option');
      opt.value = p.name;
      opt.textContent = `${p.name} — ${popLabel}${sweetTag}`;
      nearbyEl.appendChild(opt);
      if (p.population) nearbyPopMap[p.name] = p.population;
    });
    nearbyEl.disabled = false;
  } catch (e) {
    console.error('nearby-cities fetch error:', e);
    loadingTag.style.display = 'none';
    nearbyEl.innerHTML = '<option value="">Request failed — check console</option>';
    nearbyEl.disabled = true;
  }
}

function onNearbyChange() {
  const chosen = nearbyEl.value;
  if (chosen) {
    const pop = nearbyPopMap[chosen];
    if (pop) { popEl.value = pop; popTag.textContent = '(from ' + chosen + ')'; }
    else { popEl.value = ''; popTag.textContent = '(unknown — enter manually)'; }
  } else {
    popEl.value = '';
    popTag.textContent = '';
  }
  updateContinueBtn();
}

function updateContinueBtn() {
  const ok = !!stateEl.value && !!cityEl.value;
  document.getElementById('continueBtn').disabled = !ok;
}

document.getElementById('continueBtn').addEventListener('click', function() {
  const state = stateEl.value;
  const nearbyChoice = nearbyEl.value;
  const city = nearbyChoice || cityEl.value; // small city overrides main city if chosen
  const population = popEl.value;
  if (!state || !city) { alert('Please select a state and city.'); return; }
  const params = new URLSearchParams({ state, city, population: population || '' });
  window.location.href = 'audit-form.php?' + params.toString();
});
</script>
</body>
</html>
