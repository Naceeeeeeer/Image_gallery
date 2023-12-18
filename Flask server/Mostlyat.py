import matplotlib.pyplot as plt
from sklearn.cluster import KMeans
import io
import os
from PIL import Image
from flask import Flask, redirect, request, send_from_directory, jsonify, render_template, g, session
from pathlib import Path
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
import cv2
import numpy as np
from skimage import feature

app = Flask(__name__)
global weights
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
app.config['weights'] = {'hist_rgb': 1.0, 'hist_hsv': 1.0, 'mean': 1.0, 'std_dev': 1.0,
                         'contrast': 1.0, 'dissimilarity': 1.0, 'homogeneity': 1.0,
                         'energy': 1.0, 'correlation': 1.0, 'ASM': 1.0, }
weights = {'hist_rgb': 1.0, 'hist_hsv': 1.0, 'mean': 1.0, 'std_dev': 1.0,
            'contrast': 1.0, 'dissimilarity': 1.0, 'homogeneity': 1.0,
             'energy': 1.0, 'correlation': 1.0, 'ASM': 1.0, }
app.config['dict1'] = {}
app.config['results'] = []
app.config['feedback_data'] = {'relevant_images': [], 'irrelevant_images': [], }
app.config['features_local'] = []
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

    locally_features = np.concatenate((hist_rgb, hist_hsv, mean, std_dev, contrast, dissimilarity, homogeneity, energy, correlation, ASM))
    return locally_features.flatten()
def calculate_euclidean_distance(descriptor1, descriptor2):
    distance = 0.0
    for i in range(len(descriptor1)):
        distance += (descriptor1[i]-descriptor2[i])**2
    return np.sqrt(distance)
featuressum = []
featuressum_dict = []
images = []
@app.route('/')
def uploading_images():
    global featuressum, featuressum_dict, images
    dataset_path = Path("./GHIM-10K")
    categories = ["MotoCycle","Car","Beaches","FireWorks","Forest"]
    i=0
    for category in categories:
        source_path = dataset_path / category
        if source_path.exists():
          
            for image_file in source_path.iterdir():
                destination_file = dataset_path / category / image_file.name
                with Image.open(destination_file) as image:
                    image = image.resize((400, 300))
                    image = image.convert('RGB')
                    featuressum.append(extract_features(destination_file))
                    featuressum_dict.append(extract_features_dict(destination_file))
                    desc = extract_features(str(destination_file))
                    with io.BytesIO() as byte_io:
                        image.save(byte_io, format='JPEG')
                        byte_data = byte_io.getvalue()
                print(i)
                i+=1
                instance = Pictures(file_name=image_file.name, Image_bytes=byte_data,Descripteur=desc,category=category)
                db.session.add(instance)
    db.session.commit()
    images = db.session.query(Pictures).all()
    return 'done'

@app.route('/Search/Simple/', methods=['POST'])
def The_Simple_Search():
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
            print(results)
            return jsonify({'images': results.tolist()})
    else:
        pass

def extract_features_dict(path):
    image = cv2.imread(str(path))
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
    local_features = {'hist_rgb': hist_rgb, 'hist_hsv': hist_hsv, 'mean': mean, 'std_dev': std_dev,
                      'contrast': contrast, 'dissimilarity': dissimilarity, 'homogeneity': homogeneity,
                      'energy': energy, 'correlation': correlation, 'ASM': ASM, }
    return local_features
def Adjusting_weights(labeled_features, dict2, relevance, the_weights):
    keys = set(the_weights.keys())
    global weights
    for num, relevant in enumerate(relevance):
        # if relevant == '1':
        if relevant == 1:
            adjusted_weights = [0.8, 0.85, 0.9, 0.95, 1.0, 1.0, 1.05, 1.1, 1.15, 1.2]
        else:
            adjusted_weights = [1.2, 1.15, 1.1, 1.05, 1.0, 1.0, 0.95, 0.9, 0.85, 0.8]
        distance_features = {}
        # for labels in labeled_features:
        for key in keys:
                # adjusted_weights = features_weights[key]
            array1 = np.array(labeled_features[num].get(key, [0]))
            max_value_array1 = np.max(array1)
            min_value_array1 = np.min(array1)
            normalized_array1 = (array1-min_value_array1) / (max_value_array1-min_value_array1)

            array2 = np.array(dict2.get(key, [0]))
            max_value_array2 = np.max(array2)
            min_value_array2 = np.min(array2)
            normalized_array2 = (array2-min_value_array2) / (max_value_array2-min_value_array2)

            distance_features[key] = np.linalg.norm(normalized_array1 - normalized_array2)
                # distance += distance_features[key]
        sorted_dict = dict(sorted(distance_features.items(), key=lambda item: item[1]))
        i = 0
        for key,value in sorted_dict.items():
                weights[key] = adjusted_weights[i]* value
                i += 1
    # for key in keys:
    #     distance += dict1[key] * weights[key]
    return weights
    # return weights, distance
def dict_Simple_Search(featuresa, num_results):
    global featuressum_dict
    similarities = []
    for feat in featuressum_dict:
        similarities.append(euclidean_distance_dict(feat, featuresa))
    results = np.argsort(similarities)[:num_results]
    return results.tolist()
def euclidean_distance_dict(dict1, dict2):
    keys = set(dict1.keys()) | set(dict2.keys())
    distance = 0.0
    for key in keys:
        array1 = np.array(dict1.get(key, [0]))
        array2 = np.array(dict2.get(key, [0]))
        distance += np.linalg.norm(array1 - array2)
    return distance
def upload_images():
    # dataset_path = Path("../Traitement D'image/Mini Projet/GHIM-10K")
    dataset_path = Path("./")
    categories = ["Beach"]
    list_image = []
    features = []
    for category in categories:
        source_path = dataset_path / category
        if source_path.exists():
            for image_file in source_path.iterdir():
                destination_file = dataset_path / category / image_file.name
                list_image.append(image_file.name)
                desc = extract_features_dict(str(destination_file))
                features.append(desc)
    return features, list_image
def Simple_Search(featuresa, num_results):
    featuressum = []
    images = db.session.query(Pictures).all()
    for image in images:
        featuressum.append(image.Descripteur)
    similarities = []
    for feat in featuressum:
        numpy_array = np.fromstring(feat.strip('[]'), sep=' ')
        feat = numpy_array.tolist()
        similarities.append(calculate_euclidean_distance(feat,featuresa))
    results = np.argsort(similarities)[:num_results]
    return results

@app.route('/relevance-feedback/', methods=['POST'])
def upload():
    global images, featuressum, featuressum_dict, results
    uploaded_image = request.files['image']
    num_results = int(request.form.get('numOfResults'))
    print(num_results)
    image_path = "static/" + uploaded_image.filename
    print(image_path)
    uploaded_image.save(image_path)    
    featuresa = extract_features_dict(image_path)
    similarities = []
    for feat in featuressum_dict:
        similarities.append(euclidean_distance_dict(feat, featuresa))
    results = np.argsort(similarities)[:num_results]
    print(results)
    return jsonify({'images': results.tolist()})

@app.route('/relevance-feedback-results/', methods=['GET', 'POST'])
def display_and_weight_calculating():

    weights = app.config['weights']
    global images, featuressum_dict, results

    list_of_image=[i.id for i in images]

    feedback_data = app.config['feedback_data']
    data = request.json  # Assuming Laravel is sending JSON data
    relevant_list = data.get('relevant', [])
    image_id_list = data.get('image_ids', [])
    image_id = data.get('image')
    num_results =len(relevant_list)
    image = images[int(image_id)] 
    filename = image.file_name
    file_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
    old_image_features = extract_features_dict(file_path)
    for i in range(num_results):
        image_id = results[int(image_id_list[i])]
        if relevant_list[i] == '1':
            feedback_data['relevant_images'].append(image_id)
        else:
            feedback_data['irrelevant_images'].append(image_id)

    relevant_features = [featuressum_dict[list_of_image.index(i)] for i in feedback_data['relevant_images']]
    irrelevant_features = [featuressum_dict[list_of_image.index(i)] for i in feedback_data['irrelevant_images']]

    labeled_features = relevant_features + irrelevant_features
    labels = [1] * len(relevant_features) + [-1] * len(irrelevant_features)

    weights = Adjusting_weights(labeled_features, old_image_features, labels, weights)

    new_features = []
    for featureya in featuressum_dict:
        for key, value in featureya.items():
            featureya[key] = [num * weights[key] for num in value]
        new_features.append(featureya)
    features = new_features
    g.featuressum_dict = features
    results = dict_Simple_Search(old_image_features, num_results)

    listaa = []
    for res in results:
        listaa.append(list_of_image[res])
    results = listaa
    app.config['weights'] = weights
    print(results)
    return jsonify({'images': results})


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

@app.route('/extract/regions/', methods=['POST'])
def extract_similar_texture_func():
    if request.method == 'POST':
        uploaded_image = request.files['image']
        if uploaded_image:
            filename = uploaded_image.filename
            file_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            uploaded_image.save(file_path)
        return jsonify({'images': extract_similar_texture(file_path)})
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
            Histos = Histogrammes(red=str(hist_r.flatten().tolist()), blue=str(hist_b.flatten().tolist()), green=str(hist_g.flatten().tolist()))
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