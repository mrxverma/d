const apiKey = "YOUR_API_KEY"; // Replace with your YouTube Data API key

function extractVideoId(url) {
  const match = url.match(/(?:v=|\.be\/)([\w-]{11})/);
  return match ? match[1] : null;
}

async function fetchVideoInfo(id) {
  const api = `https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&id=${id}&key=${apiKey}`;
  const res = await fetch(api);
  const data = await res.json();
  return data.items && data.items.length ? data.items[0] : null;
}

function addRow(video, id) {
  const tbody = document.querySelector("#videoTable tbody");
  const row = document.createElement("tr");
  row.innerHTML = `
    <td><img src="${video.snippet.thumbnails.default.url}" alt="thumb"></td>
    <td>${video.snippet.title}</td>
    <td>${video.statistics.likeCount || 0}</td>
    <td>${video.statistics.commentCount || 0}</td>
    <td>${new Date(video.snippet.publishedAt).toLocaleDateString()}</td>
    <td><a href="https://youtu.be/${id}" target="_blank">Watch</a></td>
  `;
  tbody.appendChild(row);
}

const form = document.getElementById("addForm");
form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const url = document.getElementById("videoUrl").value.trim();
  const id = extractVideoId(url);
  if (!id) return;
  const video = await fetchVideoInfo(id);
  if (video) {
    addRow(video, id);
  }
  form.reset();
});
