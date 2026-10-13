
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from sentence_transformers import SentenceTransformer

app = FastAPI()

model = SentenceTransformer(
    "sentence-transformers/all-MiniLM-L6-v2"
)

class EmbedRequest(BaseModel):
    text: str

@app.get("/health")
def health():
    return {"status": "ok"}

@app.post("/embed")
def generate_embedding(data: EmbedRequest):
    if not data.text.strip():
        raise HTTPException(
            status_code=400,
            detail="Text tidak boleh kosong"
        )

    vector = model.encode(
        data.text,
        normalize_embeddings=True
    ).tolist()

    return {
        "embedding": vector,
        "dimensions": len(vector)
    }
