from fastapi import FastAPI
from textblob import TextBlob


app = FastAPI()

@app.get("/analysis/{content}")
async def comment_analysis(content: str):
    return {"result": getAnalysis(comment=content)}




def getAnalysis(comment):
    polarity = TextBlob(comment).sentiment.polarity
    return "Negative" if polarity < 0 else "Positive" if polarity > 0 else "Neutral"
