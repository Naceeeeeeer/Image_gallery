import matplotlib.pyplot as plt
from sklearn.cluster import KMeans
import io
import os
from PIL import Image
from flask import Flask, redirect, request, send_from_directory, jsonify, render_template
from pathlib import Path
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
import cv2
import numpy as np
from skimage import feature
from sklearn.svm import OneClassSVM

app = Flask(__name__)
cors = CORS(app, resources={r"/image/upload/": {"origins": "http://127.0.0.1:8000"}})
cors1 = CORS(app, resources={r"/image/upload2/": {"origins": "http://127.0.0.1:8000"}})
cors2 = CORS(app, resources={r"/image/upload1/": {"origins": "http://127.0.0.1:8000"}})
cors3 = CORS(app, resources={r"/Search": {"origins": "http://127.0.0.1:8000"}})
cors4 = CORS(app, resources={r"/Search/Simple": {"origins": "http://127.0.0.1:8000"}})
cors5 = CORS(app, resources={r"/Search/RF": {"origins": "http://127.0.0.1:8000"}})
app.config['SQLALCHEMY_DATABASE_URI'] = "mysql://root:@localhost/laravel"
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db = SQLAlchemy(app)
app.config['DEBUG'] = True
UPLOAD_FOLDER = 'uploads'
ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif'}
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
STATIC_FOLDER = 'static'
app.config['STATIC_FOLDER'] = STATIC_FOLDER
dict1 = {}
feedback_data = {
    'relevant_images': [],
    'irrelevant_images': [],
}
features = []
results = []
features_local = []
onclass_svm_model = OneClassSVM()


class Pictures(db.Model):
    __tablename__ = 'pictures'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    file_name = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=True)
    Image_bytes = db.Column(db.LargeBinary, nullable=False)
    Descripteur= db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=True)
    category= db.Column(db.String(20, collation='utf8mb4_unicode_ci'), nullable=True)
class Histogramme(db.Model):
    __tablename__ = 'histogramme'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    file_name = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=True)
class Histogrammes(db.Model):
    __tablename__ = 'histogrammes'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    red = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=True)
    blue = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=True)
    green = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=True)
class Pallettes(db.Model):
    __tablename__ = 'pallettes'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    file_name = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=False)
class Moments(db.Model):
    __tablename__ = 'moments'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    file_name = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=False)
def extract_similar_texture(image_path, threshold=10):
    image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
    blurred = cv2.GaussianBlur(image, (5, 5), 0)
    grad_x = cv2.Sobel(blurred, cv2.CV_64F, 1, 0, ksize=3)
    grad_y = cv2.Sobel(blurred, cv2.CV_64F, 0, 1, ksize=3)
    gradient_magnitude = np.sqrt(grad_x**2 + grad_y**2)
    _, binary_image = cv2.threshold(gradient_magnitude, threshold, 255, cv2.THRESH_BINARY)
    _, labels, stats, centroids = cv2.connectedComponentsWithStats(binary_image.astype(np.uint8))
    similar_texture_regions = []
    for stat in stats[1:]:
        x, y, w, h, area = stat
        if area > 1000:
            similar_texture_regions.append(image[y:y+h, x:x+w])
    return similar_texture_regions
def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS
# def extract_features1(path):
#     image = cv2.imread(path)
#     hist_rgb = cv2.calcHist([image], [0, 1, 2], None, [8, 8, 8], [0, 256, 0, 256, 0, 256]).flatten()
#     hsv_image = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)
#     hist_hsv = cv2.calcHist([hsv_image], [0, 1, 2], None, [8, 8, 8], [0, 256, 0, 256, 0, 256]).flatten()
#     mean, std_dev = cv2.meanStdDev(image)
#     mean, std_dev = mean.flatten(), std_dev.flatten()
#     gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
#     graycom = feature.graycomatrix(gray, [1], [0, np.pi / 4, np.pi / 2, 3 * np.pi / 4], levels=256)
#     contrast, dissimilarity, homogeneity, energy, correlation, ASM = (
#         feature.graycoprops(graycom, prop)[0] for prop in ['contrast', 'dissimilarity', 'homogeneity', 'energy', 'correlation', 'ASM']
#     )
#
#     features = np.concatenate((hist_rgb, hist_hsv, mean, std_dev,
#                                contrast, dissimilarity, homogeneity, energy, correlation, ASM))
#     return features
def extract_features_dict(path):
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
def extract_features(path):
    image = cv2.imread(str(path))

    hist_rgb = cv2.calcHist([image], [0, 1, 2], None, [8, 8, 8], [0, 256, 0, 256, 0, 256]).flatten()

    hsv_image = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)
    hist_hsv = cv2.calcHist([hsv_image], [0, 1, 2], None, [8, 8, 8], [0, 256, 0, 256, 0, 256]).flatten()

    mean, std_dev = cv2.meanStdDev(image)
    mean, std_dev = mean.flatten(), std_dev.flatten()

    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    graycom = feature.graycomatrix(gray, [1], [0, np.pi / 4, np.pi / 2, 3 * np.pi / 4], levels=256)
    contrast, dissimilarity, homogeneity, energy, correlation, ASM = (
        feature.graycoprops(graycom, prop)[0] for prop in ['contrast', 'dissimilarity', 'homogeneity', 'energy', 'correlation', 'ASM']
    )

    features = np.concatenate((hist_rgb, hist_hsv, mean, std_dev, contrast, dissimilarity, homogeneity, energy, correlation, ASM))
    return features.flatten()
def calculate_euclidean_distance(descriptor1, descriptor2):
    distance = 0.0
    for i in range(len(descriptor1)):
        distance += (descriptor1[i]-descriptor2[i])**2
    return np.sqrt(distance)
def Simple_Search_RF(feats, num_results=10):
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
def euclidean_distance(dict1, dict2):
    keys = set(dict1.keys()) | set(dict2.keys())
    distance = 0.0
    for key in keys:
        array1 = np.array(dict1.get(key, [0]))
        array2 = np.array(dict2.get(key, [0]))
        distance += np.linalg.norm(array1 - array2)
    return distance
def upload_images():
    dataset_path = Path("./GHIM-10K")
    # dataset_path = Path("./")
    categories = ["Beaches"]
    list_image=[]
    features=[]
    for category in categories:
        source_path = dataset_path / category
        if source_path.exists():
            for image_file in source_path.iterdir():
                destination_file = dataset_path / category / image_file.name
                list_image.append(image_file.name)
                desc = extract_features_dict(str(destination_file))
                features.append(desc)
    print(features)
    return features, list_image

featuressum= []
features=[]

@app.route('/')
def uploading_images():
    dataset_path = Path("./GHIM-10K")
    categories = ["Beaches","Car","FireWorks","Forest","MotoCycle"]
    for category in categories:
        source_path = dataset_path / category
        if source_path.exists():
            for image_file in source_path.iterdir():
                destination_file = dataset_path / category / image_file.name
                with Image.open(destination_file) as image:
                    image = image.resize((400, 300))
                    image = image.convert('RGB')
                    featuressum.append(extract_features(destination_file))
                    with io.BytesIO() as byte_io:
                        image.save(byte_io, format='JPEG')
                        byte_data = byte_io.getvalue()
                desc = extract_features(str(destination_file))
                instance = Pictures(file_name=image_file.name, Image_bytes=byte_data,Descripteur=desc,category=category)
                db.session.add(instance)
    db.session.commit()
    return 'done'

@app.route('/Search/Simple/', methods=['POST'])
def Simple_Search():
    i=0
    print(i)
    i+=1
    if request.method == 'POST':
        uploaded_image = request.files['image']
        print(i)
        i+=1
        if uploaded_image:
            filename = uploaded_image.filename
            print(i)
            i+=1
            file_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            uploaded_image.save(file_path)
            featuresa = extract_features(file_path)
            num_results = int(request.form.get('numOfResults'))
            featuressum = []
            images = db.session.query(Pictures).all()
            for image in images:
                featuressum.append(image.Descripteur)
            similarities=[] 
            for feat in featuressum:
                numpy_array = np.fromstring(feat.strip('[]'), sep=' ')
                feat = numpy_array.tolist()
                similarities.append(calculate_euclidean_distance(feat,featuresa))
            results = np.argsort(similarities)[:num_results]
            return jsonify({'images': results.tolist()})
    else:
        pass

@app.route('/relevance-feedback', methods=['POST'])
def upload():
    global results
    uploaded_image = request.files['image']
    global features_local
    if uploaded_image and allowed_file(uploaded_image.filename):
        image_path = "static/" + uploaded_image.filename
        uploaded_image.save(image_path)
        features_local = extract_features_dict(image_path)
        results = Simple_Search_RF(features_local)  # num_results
        print(results)
        return jsonify({'images': results})
    else:
        return "Invalid file format. Allowed formats are: png, jpg, jpeg, gif"

@app.route('/relevance-feedback-results', methods=['GET', 'POST'])
def display_results():
    global onclass_svm_model, results, features, features_local
    listaa = []
    num_results=10
    relevant_list = request.form.getlist('relevant[]')
    image_id_list = request.form.getlist('image_ids[]')
    if request.method == 'POST':
        for i in range(num_results):  #number of results
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
        tab = Simple_Search_RF(features_local, Num)

        tab1 = [int(list_of_image.index(i)) for i in tab]
        results = [int(list_of_image.index(i)) for i in feedback_data['relevant_images']] + tab1
        for res in results:
            listaa.append(list_of_image[res])
        results = listaa
        return jsonify({'images': results.tolist()})
    else:
        pass

@app.route('/extract/regions/', methods=['POST'])
def extract_similar_texture_func():
    if request.method == 'POST':
        uploaded_image = request.files['image']
        if uploaded_image:
            filename = uploaded_image.filename
            file_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            uploaded_image.save(file_path)
            similar_texture_regions = extract_similar_texture(image_path)
        return jsonify({'images': similar_texture_regions})
    else:
        pass

@app.route('/image/upload/', methods=['POST'])
def HistogrammeImage():
    if request.method == 'POST':
        uploaded_image = request.files['image']
        if uploaded_image:
            filename = uploaded_image.filename
            file_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            uploaded_image.save(file_path)
            image = cv2.imread(file_path)
            hist = cv2.calcHist([image], [0, 1, 2], None, [8, 8, 8], [0, 256, 0, 256, 0, 256])
            hist_r = cv2.calcHist([image], [2], None, [256], [0, 256])
            hist_g = cv2.calcHist([image], [1], None, [256], [0, 256])
            hist_b = cv2.calcHist([image], [0], None, [256], [0, 256])
            histogram_as_list = hist.flatten().tolist()
            Histo = Histogramme(file_name=str(histogram_as_list))
            Histos = Histogrammes(red=str(hist_r), blue=str(hist_b), green=str(hist_g))
            db.session.add(Histos)
            db.session.add(Histo)
            db.session.commit()
            plt.figure(figsize=(10, 6))
            plt.plot(hist_r, color='red', label='R')
            plt.plot(hist_g, color='green', label='G')
            plt.plot(hist_b, color='blue', label='B')
            filename1='Histogramme_'+filename + '.png'
            plt.savefig(os.path.join(app.config['STATIC_FOLDER'], filename1))
            plt.close()
            return redirect(f'/image/upload/histogramme/{filename1}')
        else:
            pass

@app.route('/image/upload/histogramme/<filename>', methods=['GET'])
def return_histogramme_image(filename):
    return send_from_directory(app.config['STATIC_FOLDER'], filename)

@app.route('/image/upload2/', methods=['POST'])
def PalletteImage():
    if request.method == 'POST':
        uploaded_image = request.files['image']
        if uploaded_image:
            filename = uploaded_image.filename
            image1 = cv2.imread(app.config['UPLOAD_FOLDER']+'/'+filename)
            pixels = np.float32(image1.reshape(-1, 3))
            n_colors = 5  # You can adjust the number of dominant colors as needed
            criteria = (cv2.TERM_CRITERIA_EPS + cv2.TERM_CRITERIA_MAX_ITER, 200, 0.1)
            _, labels, centers = cv2.kmeans(pixels, n_colors, None, criteria, 10, cv2.KMEANS_RANDOM_CENTERS)
            dominant_color = centers.astype(np.uint8).tolist()
            Pall = Pallettes(file_name=str(dominant_color))
            db.session.add(Pall)
            db.session.commit()
            pixels_rgb = cv2.cvtColor(image1, cv2.COLOR_BGR2RGB).reshape(-1, 3)
            k = 5
            kmeans_rgb = KMeans(n_clusters=k, n_init=10)
            kmeans_rgb.fit(pixels_rgb)
            colors_rgb = kmeans_rgb.cluster_centers_
            height, width = 100, 100
            num_colors = len(colors_rgb)
            image1 = np.zeros((height, width * num_colors, 3), np.uint8)
            for i, colors in enumerate(colors_rgb):
                b, g, r = colors
                start_col = i * width
                end_col = (i + 1) * width
                image1[:, start_col:end_col, 0] = b
                image1[:, start_col:end_col, 1] = g
                image1[:, start_col:end_col, 2] = r
                filename1 = 'pallette_' + filename + '.png'
            cv2.imwrite(f'static/{filename1}', image1)
            return redirect(f'/image/upload2/pallette/{filename1}')
        else:
            pass

@app.route('/image/upload2/pallette/<filename>', methods=['GET'])
def return_pallette_image(filename):
    return send_from_directory(app.config['STATIC_FOLDER'], filename)

@app.route('/image/upload1/', methods=['POST'])
def MomentImage():
    if request.method == 'POST':
        uploaded_image = request.files['image']
        if uploaded_image:
            filename = uploaded_image.filename
            image2 = cv2.imread(app.config['UPLOAD_FOLDER'] + '/' + filename)
            image_hsv = cv2.cvtColor(image2, cv2.COLOR_BGR2HSV)
            channels = cv2.split(image_hsv)
            color_moments = []
            for channel in channels:
                mean = cv2.mean(channel)
                std_dev = cv2.meanStdDev(channel)
                color_moments.extend(mean)
                color_moments.extend(std_dev)
            Mome = Moments(file_name=str(color_moments))
            db.session.add(Mome)
            db.session.commit()
            hsv = cv2.cvtColor(image2, cv2.COLOR_BGR2HSV)
            lower_blue = np.array([90, 50, 50])
            upper_blue = np.array([130, 255, 255])
            mask = cv2.inRange(hsv, lower_blue, upper_blue)
            result = cv2.bitwise_and(image2, image2, mask=mask)
            gray_result = cv2.cvtColor(result, cv2.COLOR_BGR2GRAY)
            M = cv2.moments(gray_result)
            centroid_x = int(M['m10'] / M['m00']) if M['m00'] != 0 else 0
            centroid_y = int(M['m01'] / M['m00']) if M['m00'] != 0 else 0
            image_with_centroid = cv2.cvtColor(result, cv2.COLOR_BGR2GRAY)
            cv2.circle(image_with_centroid, (centroid_x, centroid_y), 5, (0, 0, 255), -1)
            filename1 = 'Moment_' + filename + '.png'
            cv2.imwrite(f'static/{filename1}', image_with_centroid)
            return redirect(f'/image/upload1/Moment/{filename1}')
        else:
            pass

@app.route('/image/upload1/Moment/<filename>', methods=['GET'])
def return_moments_image(filename):
    return send_from_directory(app.config['STATIC_FOLDER'], filename)

if __name__ == '__main__':
        app.run()