import React, { useEffect, useState } from "react";
import { createRoot } from "react-dom/client";
import "./styles.css";
type Artifact = { id: number; type: string; url: string };
type Job = {
  job_id: string;
  status: string;
  progress: number;
  error?: string;
  artifacts: Artifact[];
};
const api = "/api/v1";
function App() {
  const [file, setFile] = useState<File | null>(null);
  const [job, setJob] = useState<Job | null>(null);
  const [busy, setBusy] = useState(false);
  async function submit() {
    if (!file) return;
    setBusy(true);
    const fd = new FormData();
    fd.append("image", file);
    const r = await fetch(`${api}/jobs`, { method: "POST", body: fd });
    if (!r.ok) {
      alert(await r.text());
      setBusy(false);
      return;
    }
    const x = await r.json();
    setJob({ ...x, progress: 0, artifacts: [] });
    setBusy(false);
  }
  useEffect(() => {
    if (!job?.job_id) return;
    const t = setInterval(async () => {
      const r = await fetch(`${api}/jobs/${job.job_id}`);
      if (r.ok) {
        const x = await r.json();
        setJob(x);
        if (["completed", "failed"].includes(x.status)) clearInterval(t);
      }
    }, 1200);
    return () => clearInterval(t);
  }, [job?.job_id]);
  return (
    <>
    
    <main>
      <section className="card">
        <h1>Room AI Visualizer</h1>
        <p className="muted">
          Upload a room photo to run segmentation, furniture detection, removal
          and furnishing.
        </p>
        <label className="drop">
          {file ? (
            <>
              <b>{file.name}</b>
              <span>{Math.round(file.size / 1024)} KB</span>
            </>
          ) : (
            <>
              <b>Choose a JPEG or PNG</b>
              <span>Maximum 10 MB</span>
            </>
          )}
          <input
            type="file"
            accept="image/jpeg,image/png"
            onChange={(e) => setFile(e.target.files?.[0] ?? null)}
          />
        </label>
        <button disabled={!file || busy} onClick={submit}>
          {busy ? "Uploading…" : "Process room"}
        </button>
      </section>
      {job && (
        <section className="card">
          <div className="row">
            <h2>Job</h2>
            <span className={`badge ${job.status}`}>{job.status}</span>
          </div>
          <div className="progress">
            <i style={{ width: `${job.progress}%` }} />
          </div>
          <p>{job.progress}% complete</p>
          {job.error && <p className="error">{job.error}</p>}
          {job.artifacts.length > 0 && (
            <div className="grid">
              {job.artifacts.map((a) => (
                <article key={a.id}>
                  <h3>{a.type.replace("_", " ")}</h3>
                  {a.type === "detections" ? (
                    <a href={a.url}>View JSON</a>
                  ) : (
                    <img src={a.url} />
                  )}
                </article>
              ))}
            </div>
          )}
        </section>
      )}
    </main>
    </>
  );
}
createRoot(document.getElementById("root")!).render(<App />);
