import sys
import os
import unidecode
import json
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from tika import parser


def readInputFile(path: str):
    with open(path) as input_file:
        raw_inputs = input_file.read()
        input_file.close()
    return json.loads(raw_inputs)


def readFiles(file_names: list):
    student_notes = []
    for file in file_names:
        raw = parser.from_file(file)
        student_notes.append(unidecode.unidecode(raw['content'].replace('\n', '')))
    return student_notes


def check_plagiarism_for_all_files(note_vectors):
    plagiarism_results = []

    index = 1
    for student_1, text_vector_1 in note_vectors:
        new_note_vectors = note_vectors.copy()[index:]

        for student_2, text_vector_2 in new_note_vectors:
            similarity_score = similarity(text_vector_1, text_vector_2)[0][1]

            output = {
                'student1': student_1,
                'student2': student_2,
                'similarityRate': similarity_score
            }
            plagiarism_results.append(output)

        index += 1

    return plagiarism_results


def check_plagiarism_for_one_file(student_note_vector, other_note_vectors):
    plagiarism_results = []

    student_1 = student_note_vector[0][0]
    text_vector_1 = student_note_vector[0][1]

    for student_2, text_vector_2 in other_note_vectors:
        similarity_score = similarity(text_vector_1, text_vector_2)[0][1]

        output = {
            'student1': student_1,
            'student2': student_2,
            'similarityRate': similarity_score
        }
        plagiarism_results.append(output)

    return plagiarism_results


vectorize = lambda Text: TfidfVectorizer().fit_transform(Text).toarray()
similarity = lambda doc1, doc2: cosine_similarity([doc1, doc2])


def run_teacher_similarity_checker():
    inputs = readInputFile(sys.argv[1])

    if len(inputs) < 2:
        return []

    os.chdir('../responses')
    student_notes = readFiles([input['filePath'] for input in inputs])

    note_vectors = list(zip(inputs, vectorize(student_notes)))

    return check_plagiarism_for_all_files(note_vectors)


def run_student_similarity_checker():
    inputs = readInputFile(sys.argv[1])

    if len(inputs['otherInputs']) < 1:
        return []

    os.chdir('../responses')
    student_notes = readFiles([inputs['studentInput']['filePath']])
    other_notes = readFiles([input['filePath'] for input in inputs['otherInputs']])

    all_notes = other_notes
    all_notes.append(student_notes[0])

    all_notes_vectorized = vectorize(all_notes)

    student_note_vector = list(zip([inputs['studentInput']], [all_notes_vectorized[-1]]))
    other_note_vector = list(zip(inputs['otherInputs'], all_notes_vectorized[:-1]))

    return check_plagiarism_for_one_file(student_note_vector, other_note_vector)


def main():
    type = sys.argv[2]
    os.chdir('../files/dto')

    if type == 'all':
        print(run_teacher_similarity_checker())
    if type == 'one':
        print(run_student_similarity_checker())


main()
