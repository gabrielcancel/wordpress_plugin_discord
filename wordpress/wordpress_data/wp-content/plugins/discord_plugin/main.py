import os
from textblob import TextBlob
import sys




def getAnalysis(comment):
    polarity = TextBlob(comment).sentiment.polarity
    return "Negative" if polarity < 0 else "Positive" if polarity > 0 else "Neutral"


if __name__ == "__main__":
    
    try: 
        content = sys.argv[1]
        print(getAnalysis(content))
    except Exception as e:
        print(e)

