const apiKey = "YOUR_API_KEY"; // Replace with your YouTube Data API key

async function fetchVideoInfo(id) {
  const api = `https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&id=${id}&key=${apiKey}`;
  const res = await fetch(api);
  const data = await res.json();
  return data.items && data.items.length ? data.items[0] : null;
}

function populateRow(row, video, id) {
  row.querySelector("img").src = video.snippet.thumbnails.default.url;
  row.querySelector(".title").textContent = video.snippet.title;
  row.querySelector(".likes").textContent = video.statistics.likeCount || 0;
  row.querySelector(".comments").textContent =
    video.statistics.commentCount || 0;
  row.querySelector(".uploaded").textContent = new Date(
    video.snippet.publishedAt,
  ).toLocaleDateString();
  const link = row.querySelector(".video-link");
  if (link) link.href = `https://youtu.be/${id}`;
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("tr[data-video-id]").forEach(async (row) => {
    const id = row.dataset.videoId;
    const video = await fetchVideoInfo(id);
    if (video) populateRow(row, video, id);
  });
});
