pipeline {
    agent { label 'ht1' }

    stages {
        stage('Build') {
            steps {

                // Print all the environment variables.
                sh 'printenv'
                sh 'echo $GIT_BRANCH'
                sh 'echo $GIT_COMMIT'
                echo 'Construction des images'
                sh 'docker pull stephdw/delaintu'
                sh 'docker-compose -f docker-compose-tu.yml build'
                echo 'Arrêt des instances précédentes '
                sh 'docker-compose -f docker-compose-tu.yml down --remove-orphans'
                echo 'Lancement du docker-compose'
                sh 'docker-compose -f docker-compose-tu.yml up -d'
            }
        }
        stage('Test') {
            steps {
                echo 'PHP Unit tests'
                sh 'web/tests/phpunit_docker-tu.sh'
            }
        }

    }
    post {
        always {
            // Always cleanup after the build.
            sh 'docker-compose -f web/docker/docker-compose-tu.yml down'
        }
    }
}