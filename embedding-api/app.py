from pinecone import Pinecone
from dotenv import load_dotenv
import os

load_dotenv()

pc = Pinecone(api_key=os.getenv("PINECONE_API_KEY"))

index_name = "database-cloud"

if not pc.has_index(index_name):
    pc.create_index_for_model(
        name=index_name,
        cloud="aws",
        region="us-east-1",
        embed={
            "model": "llama-text-embed-v2",
            "field_map": {
                "text": "chunk_text"
            }
        }
    )

index = pc.Index(index_name)

records = [
    {
        "_id": "1",
        "chunk_text": "MongoDB adalah database NoSQL berbasis dokumen."
    },
    {
        "_id": "2",
        "chunk_text": "Supabase menyediakan database PostgreSQL relasional di cloud."
    },
    {
        "_id": "3",
        "chunk_text": "Database cloud memungkinkan data disimpan dan dikelola melalui infrastruktur cloud."
    }
]

index.upsert_records(
    namespace="default",
    records=records
)

results = index.search(
    namespace="default",
    query={
        "inputs": {
            "text": "database apa yang cocok untuk menyimpan data JSON?"
        },
        "top_k": 3
    },
    fields=["chunk_text"]
)

for hit in results.result.hits:
    print(hit.score, hit.fields["chunk_text"])
