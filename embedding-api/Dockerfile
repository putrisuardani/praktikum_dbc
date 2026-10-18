
FROM python:3.11-slim

WORKDIR /app

ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

# Install PyTorch CPU-only terlebih dahulu
RUN pip install --no-cache-dir torch \
    --index-url https://download.pytorch.org/whl/cpu

# Install dependensi aplikasi
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 8001

CMD ["python", "-m", "uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8001"]
