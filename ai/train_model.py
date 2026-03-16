import pandas as pd
import joblib
from sklearn.tree import DecisionTreeClassifier
from sklearn.model_selection import train_test_split

df = pd.read_csv("ai/dataset_movies.csv")

df["score"] = (
    df["rating"]/10*0.5 +
    df["popularity"]/df["popularity"].max()*0.3 +
    df["votes"]/df["votes"].max()*0.2
)

df["recommend"] = (df["score"] > df["score"].median()).astype(int)

features = ["rating","popularity","votes"]

X = df[features]
y = df["recommend"]

X_train,X_test,y_train,y_test = train_test_split(X,y,test_size=0.2)

model = DecisionTreeClassifier(max_depth=15)

model.fit(X_train,y_train)

print("Accuracy:",model.score(X_test,y_test))

joblib.dump(model,"ai/model.pkl")

print("modelo guardado")