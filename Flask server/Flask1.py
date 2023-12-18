import numpy as np
from flask import Flask, request, redirect, send_from_directory
import matplotlib.pyplot as plt
import os
import cv2
from sklearn.cluster import KMeans
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
app = Flask(__name__)
cors = CORS(app, resources={r"/image/upload/": {"origins": "http://127.0.0.1:8000"}})
cors1 = CORS(app, resources={r"/image/upload/2": {"origins": "http://127.0.0.1:8000"}})
cors2 = CORS(app, resources={r"/image/upload/1": {"origins": "http://127.0.0.1:8000"}})
app.config['SQLALCHEMY_DATABASE_URI'] = "mysql://root:@localhost/laravel"
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db = SQLAlchemy(app)
app.config['DEBUG'] = True
UPLOAD_FOLDER = 'uploads'
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
STATIC_FOLDER = 'static'
app.config['STATIC_FOLDER'] = STATIC_FOLDER
class Histogramme(db.Model):
    __tablename__ = 'histogramme'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    file_name = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=True)
class Pallettes(db.Model):
    __tablename__ = 'pallettes'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    file_name = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=False)
class Moments(db.Model):
    __tablename__ = 'moments'
    id = db.Column(db.BigInteger, primary_key=True, autoincrement=True)
    file_name = db.Column(db.String(500, collation='utf8mb4_unicode_ci'), nullable=False)
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
            histogram_as_list = hist.flatten().tolist()
            Histo = Histogramme(file_name=str(histogram_as_list))
            db.session.add(Histo)
            db.session.commit()
            hist_r = cv2.calcHist([image], [2], None, [256], [0, 256])
            hist_g = cv2.calcHist([image], [1], None, [256], [0, 256])
            hist_b = cv2.calcHist([image], [0], None, [256], [0, 256])
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









