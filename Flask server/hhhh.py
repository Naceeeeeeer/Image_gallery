from pathlib import Path
from flask import Flask, render_template, request
import cv2
import numpy as np
from skimage import feature
from sklearn.svm import OneClassSVM

app = Flask(__name__)
feedback_data = {
    'relevant_images': [],
    'irrelevant_images': [],
}
features = []
ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif'}
app.config['UPLOAD_FOLDER'] = 'static'
# global onclass_svm_model
results = []
features_local = []
onclass_svm_model = OneClassSVM()

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS
def extract_features(path):
    image = cv2.imread(path)
    hist_rgb = cv2.calcHist([image], [0, 1, 2], None, [8, 8, 8], [0, 256, 0, 256, 0, 256]).flatten()
    hsv_image = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)
    hist_hsv = cv2.calcHist([hsv_image], [0, 1, 2], None, [8, 8, 8], [0, 256, 0, 256, 0, 256]).flatten()
    mean, std_dev = cv2.meanStdDev(image)
    mean, std_dev = mean.flatten(), std_dev.flatten()
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    graycom = feature.graycomatrix(gray, [1], [0, np.pi / 4, np.pi / 2, 3 * np.pi / 4], levels=256)
    contrast, dissimilarity, homogeneity, energy, correlation, ASM = (
        feature.graycoprops(graycom, prop)[0] for prop in
        ['contrast', 'dissimilarity', 'homogeneity', 'energy', 'correlation', 'ASM'])
    features_local = {'hist_rgb': hist_rgb, 'hist_hsv': hist_hsv, 'mean': mean, 'std_dev': std_dev,
                      'contrast': contrast, 'dissimilarity': dissimilarity, 'homogeneity': homogeneity,
                      'energy': energy, 'correlation': correlation, 'ASM': ASM, }
    return features_local
def upload_images():
    # dataset_path = Path("../Traitement D'image/Mini Projet/GHIM-10K")
    dataset_path = Path("./")
    categories = ["Beaches"]
    list_image=[]
    features=[]
    for category in categories:
        source_path = dataset_path / category
        if source_path.exists():
            for image_file in source_path.iterdir():
                destination_file = dataset_path / category / image_file.name
                list_image.append(image_file.name)
                desc = extract_features(str(destination_file))
                features.append(desc)
    return features, list_image
def euclidean_distance(dict1, dict2):
    keys = set(dict1.keys()) | set(dict2.keys())
    distance = 0.0
    for key in keys:
        array1 = np.array(dict1.get(key, [0]))
        array2 = np.array(dict2.get(key, [0]))
        distance += np.linalg.norm(array1 - array2)
    return distance
def Simple_Search(feats, num_results=10):
    global list_of_image, features
    features, list_of_image = upload_images()
    lista = []
    features_values = [{key: image[key] for key in image} for image in features]
    distances = [euclidean_distance(feats, d) for d in features_values]
    results = np.argsort(distances)[:num_results]
    for res in results:
        lista.append(list_of_image[res])
    print("Done!")
    return lista

@app.route('/relevance-feedback', methods=['POST'])
def upload():
    global results
    uploaded_image = request.files['image']
    global features_local
    # num_results = int(request.form['num_results'])
    if uploaded_image and allowed_file(uploaded_image.filename):
        image_path = "static/" + uploaded_image.filename
        uploaded_image.save(image_path)
        features_local = extract_features(image_path)
        results = Simple_Search(features_local)  # num_results
        return render_template('results.html', image_path=results, fe=features_local)
    else:
        return "Invalid file format. Allowed formats are: png, jpg, jpeg, gif"

@app.route('/display-results', methods=['GET', 'POST'])
def display_results():
    global onclass_svm_model, results, features, features_local
    listaa = []

    relevant_list = request.form.getlist('relevant[]')
    image_id_list = request.form.getlist('image_ids[]')
    if request.method == 'POST':
        for i in range(10):  #number of results
            image_id = results[int(image_id_list[i])]
            if relevant_list[i] == '1':
                feedback_data['relevant_images'].append(image_id)
            else:
                feedback_data['irrelevant_images'].append(image_id)

        relevant_features = [list(features[list_of_image.index(i)].values()) for i in feedback_data['relevant_images']]
        irrelevant_features = [list(features[list_of_image.index(i)].values()) for i in feedback_data['irrelevant_images']]
        labeled_features = relevant_features + irrelevant_features
        labels = [1] * len(relevant_features) + [-1] * len(irrelevant_features)
        features_array = []

        for f in labeled_features:
            for t in f:
                for d in t:
                    features_array.append(d)

        my_array = np.array(features_array)
        features_array_h = my_array.reshape(-1, 1)
        labels_array = np.array(list(labels))
        onclass_svm_model.fit(features_array_h, labels_array)

        if onclass_svm_model.kernel == 'linear':
            feature_weights = onclass_svm_model.coef_.flatten()
        else:
            support_vectors_weights = np.abs(onclass_svm_model.dual_coef_.flatten())
            feature_weights = np.dot(support_vectors_weights, onclass_svm_model.support_vectors_)
        feature_values = feature_weights*2 / 1000000

        for i, image in enumerate(features):
            for key, value in image.items():
                features[i][key] = [num * feature_values for num in value]
        Num = 10-len(relevant_features)
        tab = Simple_Search(features_local, Num)

        tab1 = [int(list_of_image.index(i)) for i in tab]
        results = [int(list_of_image.index(i)) for i in feedback_data['relevant_images']] + tab1
        for res in results:
            listaa.append(list_of_image[res])
        results = listaa
        return render_template('results.html', image_path=results)
    return render_template('results.html', image_list=[])


if __name__ == '__main__':
    app.run(debug=True)
